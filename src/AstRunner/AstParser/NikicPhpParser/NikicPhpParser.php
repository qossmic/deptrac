<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceCacheInterface;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\AstRunner\Resolver\ClassDependencyResolver;

class NikicPhpParser implements AstParserInterface
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
     * @var AstFileReferenceCacheInterface
     */
    private $cache;

    /**
     * @var ClassDependencyResolver[]
     */
    private $classDependencyResolvers;

    public function __construct(
        FileParser $fileParser,
        AstFileReferenceCacheInterface $cache,
        iterable $classDependencyResolvers = []
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
            throw new \LogicException('parser not supported');
        }

        if (null !== $fileReference = $this->cache->get($data->getRealPath())) {
            return $fileReference;
        }

        $fileReference = new AstFileReference($data->getRealPath());

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new AstClassReferenceResolver($fileReference, $this->classDependencyResolvers));

        $traverser->traverse($this->fileParser->parse($data));

        $this->cache->set($fileReference);

        return $fileReference;
    }

    public function getAstForClassReference(AstClassReference $classReference): ?Node
    {
        if (isset(self::$classAstMap[$classReference->getClassName()])) {
            return self::$classAstMap[$classReference->getClassName()];
        }

        if (null === $classReference->getFileReference()) {
            return null;
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());

        $ast = $traverser->traverse(
            $this->fileParser->parse(new \SplFileInfo($classReference->getFileReference()->getFilepath()))
        );

        foreach (AstHelper::findClassLikeNodes($ast) as $classLikeNode) {
            if (isset($classLikeNode->namespacedName) && $classLikeNode->namespacedName instanceof Node\Name) {
                $className = $classLikeNode->namespacedName->toString();
            } else {
                $className = (string) $classLikeNode->name;
            }

            self::$classAstMap[$className] = $classLikeNode;
        }

        return self::$classAstMap[$classReference->getClassName()] ?? null;
    }

    /**
     * @param Node[]|array<Node[]> $nodes
     *
     * @return Node[]
     */
    public function findNodesOfType(array $nodes, string $type): array
    {
        $collectedNodes = [];

        foreach ($nodes as $node) {
            if (is_array($node)) {
                $nodesOfType = $this->findNodesOfType($node, $type);
                foreach ($nodesOfType as $n) {
                    $collectedNodes[] = $n;
                }
            } elseif ($node instanceof Node) {
                if (is_a($node, $type, true)) {
                    $collectedNodes[] = $node;
                }

                $nodesOfType = $this->findNodesOfType(AstHelper::getSubNodes($node), $type);
                foreach ($nodesOfType as $n) {
                    $collectedNodes[] = $n;
                }
            }
        }

        return $collectedNodes;
    }
}
