<?php

declare (strict_types=1);
namespace DEPTRAC_202402\PhpParser\Node\Expr\AssignOp;

use DEPTRAC_202402\PhpParser\Node\Expr\AssignOp;
class Plus extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Plus';
    }
}
