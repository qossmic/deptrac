<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\DependencyEmitter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\DependencyEmitter\InheritanceDependencyEmitter;

class InheritanceDependencyEmitterTest extends TestCase
{
    use EmitterTrait;

    public function testGetName(): void
    {
        static::assertEquals('InheritanceDependencyEmitter', (new InheritanceDependencyEmitter())->getName());
    }

    public function testApplyDependencies(): void
    {
        $deps = $this->getDeps(
            new InheritanceDependencyEmitter(),
            new \SplFileInfo(__DIR__.'/Fixtures/Foo.php')
        );

        static::assertCount(4, $deps);
        static::assertContains('Foo\Bar:6 on Foo\BarExtends', $deps);
        static::assertContains('Foo\Bar:6 on Foo\BarInterface1', $deps);
        static::assertContains('Foo\Bar:6 on BarInterface2', $deps);
        static::assertContains('Foo\Bar:8 on Foo\SomeTrait', $deps);
    }
}
