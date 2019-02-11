<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInheritInterface;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceInterface;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParserInterface;

class NikicPhpParser implements AstParserInterface
{
    private static $inheritanceByClassnameMap = [];
    private static $fileAstMap = [];

    /**
     * @var Node\Stmt\ClassLike[]
     */
    private static $classAstMap = [];

    private $fileParser;

    public function __construct(FileParserInterface $fileParser)
    {
        $this->fileParser = $fileParser;
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

        $fileReference = new AstFileReference($data->getRealPath());

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new AstClassReferenceResolver($fileReference));

        $ast = $traverser->traverse(
            $this->fileParser->parse($data)
        );

        self::$fileAstMap[$data->getRealPath()] = $ast;

        foreach (AstHelper::findClassLikeNodes($ast) as $classLikeNode) {
            if (isset($classLikeNode->namespacedName) && $classLikeNode->namespacedName instanceof Node\Name) {
                $className = $classLikeNode->namespacedName->toString();
            } else {
                $className = (string) $classLikeNode->name;
            }

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
