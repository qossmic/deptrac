<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\DependencyEmitter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\DependencyEmitter\InheritanceDependencyEmitter;

final class InheritanceDependencyEmitterTest extends TestCase
{
    use EmitterTrait;

    public function testGetName(): void
    {
        self::assertEquals('InheritanceDependencyEmitter', (new InheritanceDependencyEmitter())->getName());
    }

    public function testApplyDependencies(): void
    {
        $deps = $this->getDeps(
            new InheritanceDependencyEmitter(),
            __DIR__.'/Fixtures/Foo.php'
        );

        self::assertCount(4, $deps);
        self::assertContains('Foo\Bar:6 on Foo\BarExtends', $deps);
        self::assertContains('Foo\Bar:6 on Foo\BarInterface1', $deps);
        self::assertContains('Foo\Bar:6 on BarInterface2', $deps);
        self::assertContains('Foo\Bar:8 on Foo\SomeTrait', $deps);
    }
}
