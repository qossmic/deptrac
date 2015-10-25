<?php 

namespace DependencyTracker;

use PhpParser\Node;
use PhpParser\NodeVisitor;

class AstHelper implements NodeVisitor
{
    public static function findAstNodesOfType($ast, array $types) {
        $traverser = new \PhpParser\NodeTraverser;
        $collectedNodes = new \ArrayObject();
        $traverser->addVisitor(new static(function(Node $node) use ($collectedNodes) {
            $collectedNodes[] = $node;
        }, $types));
        $traverser->traverse($ast);

        return $collectedNodes->getArrayCopy();
    }

    private $cb;

    private $types;

    protected function __construct($cb, array $types)
    {
        $this->cb = [$cb];
        $this->types = $types;
    }

    public function beforeTraverse(array $nodes)
    {
        // TODO: Implement beforeTraverse() method.
    }

    public function enterNode(Node $node)
    {
        foreach ($this->types as $type) {
            if (is_a($node, $type, true)) {
                $this->cb[0]($node);
            }
        }
    }

    public function leaveNode(Node $node)
    {
        // TODO: Implement leaveNode() method.
    }

    public function afterTraverse(array $nodes)
    {
        // TODO: Implement afterTraverse() method.
    }


}
