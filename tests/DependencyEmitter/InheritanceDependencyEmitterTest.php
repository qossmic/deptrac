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

        static::assertSame(
            [
                'Foo\Bar:26 on Foo\BarExtends',
                'Foo\Bar:26 on Foo\BarInterface1',
                'Foo\Bar:26 on BarInterface2',
                'Foo\Bar:28 on Foo\SomeTrait',
            ],
            $deps
        );
    }
}
