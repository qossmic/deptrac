<?php

declare (strict_types=1);
namespace DEPTRAC_202401\PHPStan\PhpDocParser\Ast;

/**
 * Inspired by https://github.com/nikic/PHP-Parser/tree/36a6dcd04e7b0285e8f0868f44bd4927802f7df1
 *
 * Copyright (c) 2011, Nikita Popov
 * All rights reserved.
 */
abstract class AbstractNodeVisitor implements NodeVisitor
{
    public function beforeTraverse(array $nodes) : ?array
    {
        return null;
    }
    public function enterNode(Node $node)
    {
        return null;
    }
    public function leaveNode(Node $node)
    {
        return null;
    }
    public function afterTraverse(array $nodes) : ?array
    {
        return null;
    }
}
