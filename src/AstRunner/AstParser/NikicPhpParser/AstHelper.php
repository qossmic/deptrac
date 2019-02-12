<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;

class AstHelper
{
    public static $cacheFindClassLikeNodes = [];

    /**
     * @param Node[]|array<Node[]> $nodes
     *
     * @return Node\Stmt\ClassLike[]
     */
    public static function findClassLikeNodes(array $nodes): array
    {
        $collectedNodes = [];

        foreach ($nodes as $node) {
            if ($node instanceof Node\Stmt\ClassLike) {
                $collectedNodes[] = $node;
            } elseif ($node instanceof Use_) {
                continue;
            } elseif (is_array($node)) {
                foreach (static::findClassLikeNodes($node) as $n) {
                    $collectedNodes[] = $n;
                }
            } elseif ($node instanceof Node) {
                foreach (static::findClassLikeNodes(static::getSubNodes($node)) as $n) {
                    $collectedNodes[] = $n;
                }
            }
        }

        return $collectedNodes;
    }

    public static function getSubNodes(Node $node): array
    {
        $subnodes = [];
        foreach ($node->getSubNodeNames() as $name) {
            $subnodes[] = $node->$name;
        }

        return $subnodes;
    }
}
