<?php

declare (strict_types=1);
namespace DEPTRAC_202401\PhpParser\Node\Expr\BinaryOp;

use DEPTRAC_202401\PhpParser\Node\Expr\BinaryOp;
class LogicalXor extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return 'xor';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_LogicalXor';
    }
}
