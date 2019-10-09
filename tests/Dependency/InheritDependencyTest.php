<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\Dependency\DependencyInterface;
use SensioLabs\Deptrac\Dependency\InheritDependency;

class InheritDependencyTest extends TestCase
{
    public function testGetSet(): void
    {
        $dependency = new InheritDependency(
            '/home/src/filePath',
            'a',
            'b',
            $dep = $this->prophesize(DependencyInterface::class)->reveal(),
            $astInherit = $this->prophesize(AstInherit::class)->reveal()
        );

        static::assertSame('/home/src/filePath', $dependency->getFilename());
        static::assertEquals('a', $dependency->getClassA());
        static::assertEquals('b', $dependency->getClassB());
        static::assertEquals(0, $dependency->getClassALine());
        static::assertEquals($dep, $dependency->getOriginalDependency());
        static::assertSame($astInherit, $dependency->getPath());
    }
}
