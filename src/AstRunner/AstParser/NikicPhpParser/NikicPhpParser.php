<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInheritInterface;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceInterface;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParserInterface;

class NikicPhpParser implements AstParserInterface
{
    private static $parser = null;
    private static $traverser = null;
    private static $inheritanceByClassnameMap = [];
    private static $fileAstMap = [];

    /**
     * @var Node\Stmt\ClassLike[]
     */
    private static $classAstMap = [];

    private static function getParser(): Parser
    {
        if (!self::$parser) {
            self::$parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        }

        return self::$parser;
    }

    private static function getTraverser(): NodeTraverser
    {
        if (!self::$traverser) {
            self::$traverser = new NodeTraverser();
            self::$traverser->addVisitor(new NameResolver());
        }

        return self::$traverser;
    }

    public function supports($data): bool
    {
        if (!$data instanceof \SplFileInfo) {
            return false;
        }

        return 'php' === strtolower($data->getExtension());
    }

    public function parse($data): AstFileReferenceInterface
    {
        /** @var \SplFileInfo $data */
        if (!$this->supports($data)) {
            throw new \LogicException();
        }

        $ast = self::getTraverser()->traverse(
            (array) self::getParser()->parse(
                file_get_contents($data->getPathname())
            )
        );

        self::$fileAstMap[$data->getRealPath()] = $ast;

        $fileReference = new AstFileReference($data->getRealPath());

        foreach (AstHelper::findClassLikeNodes($ast) as $classLikeNode) {
            if (isset($classLikeNode->namespacedName) && $classLikeNode->namespacedName instanceof Node\Name) {
                $className = $classLikeNode->namespacedName->toString();
            } else {
                $className = (string) $classLikeNode->name;
            }

            $fileReference->addClassReference($className);
            self::$classAstMap[$className] = $classLikeNode;
        }

        return $fileReference;
    }

    /**
     * @return Node[]
     */
    public function getAstByFile(AstFileReferenceInterface $astReference): array
    {
        return self::$fileAstMap[$astReference->getFilepath()] ?? [];
    }

    public function getAstForClassname(string $className): ?Node
    {
        return self::$classAstMap[$className] ?? null;
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
                $collectedNodes = array_merge(
                    $this->findNodesOfType($node, $type),
                    $collectedNodes
                );
            } elseif ($node instanceof Node) {
                if (is_a($node, $type, true)) {
                    $collectedNodes[] = $node;
                }

                $collectedNodes = array_merge(
                    $this->findNodesOfType(
                        AstHelper::getSubNodes($node),
                        $type
                    ),
                    $collectedNodes
                );
            }
        }

        return $collectedNodes;
    }

    /**
     * @return AstInheritInterface[]
     */
    public function findInheritanceByClassname(string $className): array
    {
        if (isset(self::$inheritanceByClassnameMap[$className])) {
            return self::$inheritanceByClassnameMap[$className];
        }

        if (!isset(self::$classAstMap[$className])) {
            return self::$inheritanceByClassnameMap[$className] = [];
        }

        return self::$inheritanceByClassnameMap[$className] = AstHelper::findInheritances(
            self::$classAstMap[$className]
        );
    }
}
