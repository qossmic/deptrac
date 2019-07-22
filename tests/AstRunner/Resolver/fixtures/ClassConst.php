<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Integration\fixtures;

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
