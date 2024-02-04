<?php

declare (strict_types=1);
namespace DEPTRAC_202402\PhpParser\Node\Expr\Cast;

use DEPTRAC_202402\PhpParser\Node\Expr\Cast;
class Unset_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Unset';
    }
}
