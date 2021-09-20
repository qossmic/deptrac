<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\AstRunner\Fixtures\MethodCall;

class DummyClassA
{
    public function returnViolationClass(): DummyViolationClass
    {
        return new DummyViolationClass();
    }
}
