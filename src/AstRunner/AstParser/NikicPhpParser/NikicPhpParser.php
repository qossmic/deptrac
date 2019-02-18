<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParserInterface;

class NikicPhpParser implements AstParserInterface
{
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

    public function parse($data): AstFileReference
    {
        /** @var \SplFileInfo $data */
        if (!$this->supports($data)) {
            throw new \LogicException('parser not supported');
        }

        $fileReference = new AstFileReference($data->getRealPath());

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new AstClassReferenceResolver($fileReference));

        $ast = $traverser->traverse(
            $this->fileParser->parse($data)
        );

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
}
