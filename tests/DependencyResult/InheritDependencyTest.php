<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\DependencyResult;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInheritInterface;
use SensioLabs\Deptrac\DependencyResult\DependencyInterface;
use SensioLabs\Deptrac\DependencyResult\InheritDependency;

class InheritDependencyTest extends TestCase
{
    public function testGetSet(): void
    {
        $dependency = new InheritDependency(
            'a',
            'b',
            $dep = $this->prophesize(DependencyInterface::class)->reveal(),
            $astInherit = $this->prophesize(AstInheritInterface::class)->reveal()
        );

        static::assertEquals('a', $dependency->getClassA());
        static::assertEquals('b', $dependency->getClassB());
        static::assertEquals(0, $dependency->getClassALine());
        static::assertEquals($dep, $dependency->getOriginalDependency());
        static::assertSame($astInherit, $dependency->getPath());
    }
}
