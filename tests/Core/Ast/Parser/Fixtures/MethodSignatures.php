<?php

namespace Tests\Qossmic\Deptrac\Core\Ast\Parser\Fixtures;

class MethodSignaturesA
{
    public function foo() {}
}

class MethodSignaturesB
{
    public function getA(): ?MethodSignaturesA
    {
        // no-op
        return null;
    }
}

class MethodSignaturesC
{
    public function test( MethodSignaturesB $b )
    {
        $a = $b->getA();

        // Not tracked yet:
        $a->foo();
    }

}

