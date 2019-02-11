<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\Use_;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInheritInterface;

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

    public static function walkNodes(array $nodes, callable $cb, \ArrayObject $bag = null): \ArrayObject
    {
        if (!$bag) {
            $bag = new \ArrayObject();
        }

        foreach ($nodes as $i => &$node) {
            if (is_array($node)) {
                static::walkNodes($node, $cb, $bag);
            } elseif ($node instanceof Node) {
                if ($cb($node)) {
                    $bag->append($node);
                }

                static::walkNodes(static::getSubNodes($node), $cb, $bag);
            }
        }

        return $bag;
    }

    /**
     * @return AstInheritInterface[]
     */
    public static function findInheritances(Node\Stmt\ClassLike $klass): array
    {
        $buffer = [];

        if ($klass instanceof Class_
            && isset($klass->namespacedName)
            && $klass->namespacedName instanceof Name
        ) {
            if ($klass->extends instanceof Name) {
                $buffer[] = AstInherit::newExtends(
                    $klass->extends->toString(),
                    $klass->extends->getLine()
                );
            }

            if (!empty($klass->implements)) {
                foreach ($klass->implements as $impl) {
                    if (!$impl instanceof Name) {
                        continue;
                    }

                    $buffer[] = AstInherit::newImplements(
                        $impl->toString(),
                        $impl->getLine()
                    );
                }
            }
        }

        if ($klass instanceof Trait_ || $klass instanceof Class_) {
            foreach ($klass->stmts as $traitUses) {
                if (!$traitUses instanceof Node\Stmt\TraitUse) {
                    continue;
                }

                foreach ($traitUses->traits as $traitUsage) {
                    if (!$traitUsage instanceof FullyQualified) {
                        continue;
                    }

                    $buffer[] = AstInherit::newUses(
                        $traitUsage->toString(),
                        $traitUsage->getLine()
                    );
                }
            }
        }

        if ($klass instanceof Interface_ && isset($klass->namespacedName) && $klass->namespacedName instanceof Name) {
            foreach ($klass->extends as $extends) {
                $buffer[] = AstInherit::newExtends(
                    $extends->toString(),
                    $extends->getLine()
                );
            }
        }

        return $buffer;
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
