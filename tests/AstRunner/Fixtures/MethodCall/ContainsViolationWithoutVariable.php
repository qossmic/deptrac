<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\AstRunner\Fixtures\MethodCall;


class ContainsViolationWithoutVariable
{
    public function doSomething()
    {
        $classA = new DummyClassA();
        $classA->returnViolationClass()->foo();
    }
}
