<?php

declare (strict_types=1);
namespace DEPTRAC_202403\PHPStan\PhpDocParser\Ast\NodeVisitor;

use DEPTRAC_202403\PHPStan\PhpDocParser\Ast\AbstractNodeVisitor;
use DEPTRAC_202403\PHPStan\PhpDocParser\Ast\Attribute;
use DEPTRAC_202403\PHPStan\PhpDocParser\Ast\Node;
final class CloningVisitor extends AbstractNodeVisitor
{
    public function enterNode(Node $originalNode)
    {
        $node = clone $originalNode;
        $node->setAttribute(Attribute::ORIGINAL_NODE, $originalNode);
        return $node;
    }
}
