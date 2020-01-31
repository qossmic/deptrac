<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\FindingVisitor;
use PhpParser\NodeVisitor\NameResolver;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceCache;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParser;
use SensioLabs\Deptrac\AstRunner\Resolver\ClassDependencyResolver;

class NikicPhpParser implements AstParser
{
    /**
     * @var Node\Stmt\ClassLike[]
     */
    private static $classAstMap = [];

    /**
     * @var FileParser
     */
    private $fileParser;

    /**
     * @var AstFileReferenceCache
     */
    private $cache;

    /**
     * @var ClassDependencyResolver[]
     */
    private $classDependencyResolvers;

    public function __construct(
        FileParser $fileParser,
        AstFileReferenceCache $cache,
        ClassDependencyResolver ...$classDependencyResolvers
    ) {
        $this->fileParser = $fileParser;
        $this->cache = $cache;
        $this->classDependencyResolvers = $classDependencyResolvers;
    }

    public function supports($data): bool
    {
        if (!$data instanceof \SplFileInfo) {
            return false;
        }

        return 'php' === strtolower($data->getExtension());
    }

    public function parse($data): AstFileReference
    {
        /** @var \SplFileInfo $data */
        if (!$this->supports($data)) {
            throw new \LogicException('data not supported');
        }

        $realPath = (string) $data->getRealPath();
        if (null !== $fileReference = $this->cache->get($realPath)) {
            return $fileReference;
        }

        $fileReference = new AstFileReference($realPath);

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new AstClassReferenceResolver($fileReference, ...$this->classDependencyResolvers));

        $traverser->traverse($this->fileParser->parse($data));

        $this->cache->set($fileReference);

        return $fileReference;
    }

    public function getAstForClassReference(AstClassReference $classReference): ?Node\Stmt\ClassLike
    {
        $classLikeName = (string) $classReference->getClassName();

        if (isset(self::$classAstMap[$classLikeName])) {
            return self::$classAstMap[$classLikeName];
        }

        if (null === $classReference->getFileReference()) {
            return null;
        }

        $finding = new FindingVisitor(
            static function (Node $node): bool {
                return $node instanceof Node\Stmt\ClassLike;
            }
        );

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor($finding);
        $traverser->traverse(
            $this->fileParser->parse(new \SplFileInfo($classReference->getFileReference()->getFilepath()))
        );

        /** @var Node\Stmt\ClassLike[] $classLikeNodes */
        $classLikeNodes = $finding->getFoundNodes();

        foreach ($classLikeNodes as $classLikeNode) {
            if (isset($classLikeNode->namespacedName) && $classLikeNode->namespacedName instanceof Node\Name) {
                $className = $classLikeNode->namespacedName->toString();
            } else {
                $className = (string) $classLikeNode->name;
            }

            self::$classAstMap[$className] = $classLikeNode;
        }

        return self::$classAstMap[$classLikeName] ?? null;
    }
}
