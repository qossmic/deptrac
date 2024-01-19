<?php

declare (strict_types=1);
namespace DEPTRAC_202401\PhpParser\Node\Stmt;

use DEPTRAC_202401\PhpParser\Node;
abstract class TraitUseAdaptation extends Node\Stmt
{
    /** @var Node\Name|null Trait name */
    public $trait;
    /** @var Node\Identifier Method name */
    public $method;
}
