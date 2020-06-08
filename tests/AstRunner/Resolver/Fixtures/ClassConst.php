<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner\Resolver\Fixtures;

class ClassA
{
}

class ClassB
{
    public function foo()
    {
        return ClassA::class;
    }
}
