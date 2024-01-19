<?php

declare (strict_types=1);
namespace DEPTRAC_202401\PhpParser\Node\Expr\BinaryOp;

use DEPTRAC_202401\PhpParser\Node\Expr\BinaryOp;
class NotEqual extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '!=';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_NotEqual';
    }
}
