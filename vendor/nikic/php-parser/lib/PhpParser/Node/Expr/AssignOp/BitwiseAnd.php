<?php

declare (strict_types=1);
namespace DEPTRAC_202401\PhpParser\Node\Expr\AssignOp;

use DEPTRAC_202401\PhpParser\Node\Expr\AssignOp;
class BitwiseAnd extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_BitwiseAnd';
    }
}
