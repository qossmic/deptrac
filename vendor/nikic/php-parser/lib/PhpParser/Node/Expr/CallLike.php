<?php

declare (strict_types=1);
namespace DEPTRAC_202401\PhpParser\Node\Expr;

use DEPTRAC_202401\PhpParser\Node\Arg;
use DEPTRAC_202401\PhpParser\Node\Expr;
use DEPTRAC_202401\PhpParser\Node\VariadicPlaceholder;
abstract class CallLike extends Expr
{
    /**
     * Return raw arguments, which may be actual Args, or VariadicPlaceholders for first-class
     * callables.
     *
     * @return array<Arg|VariadicPlaceholder>
     */
    public abstract function getRawArgs() : array;
    /**
     * Returns whether this call expression is actually a first class callable.
     */
    public function isFirstClassCallable() : bool
    {
        foreach ($this->getRawArgs() as $arg) {
            if ($arg instanceof VariadicPlaceholder) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * Assert that this is not a first-class callable and return only ordinary Args.
     *
     * @return Arg[]
     */
    public function getArgs() : array
    {
        \assert(!$this->isFirstClassCallable());
        return $this->getRawArgs();
    }
}
