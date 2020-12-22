<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\FindingVisitor;
use PhpParser\NodeVisitor\NameResolver;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\FileReferenceBuilder;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceCache;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParser;
use SensioLabs\Deptrac\AstRunner\Resolver\ClassDependencyResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;

class NikicPhpParser implements AstParser
{
    /**
     * @var array<string, Node\Stmt\ClassLike>
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
     * @var TypeResolver
     */
    private $typeResolver;

    /**
     * @var ClassDependencyResolver[]
     */
    private $classDependencyResolvers;

    /**
     * @var NodeTraverser
     */
    private $traverser;

    public function __construct(
        FileParser $fileParser,
        AstFileReferenceCache $cache,
        TypeResolver $typeResolver,
        ClassDependencyResolver ...$classDependencyResolvers
    ) {
        $this->fileParser = $fileParser;
        $this->cache = $cache;
        $this->typeResolver = $typeResolver;
        $this->classDependencyResolvers = $classDependencyResolvers;

        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new NameResolver());
    }

    public function parse(\SplFileInfo $data): AstFileReference
    {
        $realPath = (string) $data->getRealPath();
        if (null !== $fileReference = $this->cache->get($realPath)) {
            return $fileReference;
        }

        $fileReferenceBuilder = FileReferenceBuilder::create($realPath);
        $visitor = new ClassReferenceVisitor($fileReferenceBuilder, $this->typeResolver, ...$this->classDependencyResolvers);

        $this->traverser->addVisitor($visitor);
        $this->traverser->traverse($this->fileParser->parse($data));
        $this->traverser->removeVisitor($visitor);

        $fileReference = $fileReferenceBuilder->build();
        $this->cache->set($fileReference);

        return $fileReference;
    }

    public function getAstForClassReference(AstClassReference $classReference): ?Node\Stmt\ClassLike
    {
        $classLikeName = $classReference->getClassLikeName()->toString();

        if (isset(self::$classAstMap[$classLikeName])) {
            return self::$classAstMap[$classLikeName];
        }

        $astFileReference = $classReference->getFileReference();

        if (null === $astFileReference) {
            return null;
        }

        $findingVisitor = new FindingVisitor(
            static function (Node $node): bool {
                return $node instanceof Node\Stmt\ClassLike;
            }
        );

        $this->traverser->addVisitor($findingVisitor);
        $this->traverser->traverse($this->fileParser->parse(new \SplFileInfo($astFileReference->getFilepath())));
        $this->traverser->removeVisitor($findingVisitor);

        /** @var Node\Stmt\ClassLike[] $classLikeNodes */
        $classLikeNodes = $findingVisitor->getFoundNodes();

        foreach ($classLikeNodes as $classLikeNode) {
            if (isset($classLikeNode->namespacedName)) {
                $className = $classLikeNode->namespacedName->toCodeString();
            } elseif ($classLikeNode->name instanceof Node\Identifier) {
                $className = $classLikeNode->name->toString();
            } else {
                continue;
            }

            self::$classAstMap[$className] = $classLikeNode;
        }

        return self::$classAstMap[$classLikeName] ?? null;
    }
}
