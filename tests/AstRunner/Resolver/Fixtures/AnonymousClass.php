<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner\Resolver\Fixtures;

interface InterfaceC
{
}

class ClassA
{
}

class ClassB
{
    public function foo()
    {
        return new class() extends ClassA implements InterfaceC {
        };
    }
}
