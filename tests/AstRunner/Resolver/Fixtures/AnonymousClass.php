<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner\Resolver\Fixtures;

interface InterfaceC
{
}

final class ClassA
{
}

final class ClassB
{
    public function foo()
    {
        return new class() extends ClassA implements InterfaceC {
        };
    }
}
