<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\DependencyResult;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\DependencyResult\Dependency;

class DependencyTest extends TestCase
{
    public function testGetSet(): void
    {
        $dependency = new Dependency('a', 23, 'b');
        static::assertEquals('a', $dependency->getClassA());
        static::assertEquals(23, $dependency->getClassALine());
        static::assertEquals('b', $dependency->getClassB());
    }
}
