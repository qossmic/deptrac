<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\AstRunner\Fixtures\MethodCall;


class ContainsViolationWithVariable
{
    public function doSomething()
    {
        $classA = new DummyClassA();
        $violationClass = $classA->returnViolationClass();
        $violationClass->foo();
    }
}
