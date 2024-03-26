<?php

declare (strict_types=1);
namespace DEPTRAC_202403\PHPStan\PhpDocParser\Ast\ConstExpr;

use DEPTRAC_202403\PHPStan\PhpDocParser\Ast\NodeAttributes;
class ConstExprTrueNode implements ConstExprNode
{
    use NodeAttributes;
    public function __toString() : string
    {
        return 'true';
    }
}