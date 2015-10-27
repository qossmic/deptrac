<?php 

namespace DependencyTracker;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeVisitor;

class AstHelper
{
    public static function findClassLikeNodes($nodes)
    {
        $collectedNodes = [];

        foreach ($nodes as $i => &$node) {
            if ($node instanceof Node\Stmt\ClassLike) {
                $collectedNodes[] = $node;
            } elseif ($node instanceof Use_) {
                continue;
            } elseif (is_array($node)) {
                $collectedNodes = array_merge(static::findClassLikeNodes($node), $collectedNodes);
            } elseif ($node instanceof Node) {
                $collectedNodes = array_merge(static::findClassLikeNodes(
                    static::getSubNodes($node)
                ), $collectedNodes);
            }
        }

        return $collectedNodes;
    }

    private static function getSubNodes(Node $node)
    {
        $subnodes = [];
        foreach ($node->getSubNodeNames() as $name) {
            $subnodes[] =& $node->$name;
        }
        return $subnodes;
    }
}
