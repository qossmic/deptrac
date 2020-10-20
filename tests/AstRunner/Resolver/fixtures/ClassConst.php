<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Integration\fixtures;

final class ClassA
{
}

final class ClassB
{
    public function foo()
    {
        return ClassA::class;
    }
}
