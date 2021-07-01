<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\DependencyEmitter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\DependencyEmitter\UsesDependencyEmitter;

final class UsesDependencyEmitterTest extends TestCase
{
    use EmitterTrait;

    public function testGetName(): void
    {
        self::assertEquals('UsesDependencyEmitter', (new UsesDependencyEmitter())->getName());
    }

    public function testApplyDependencies(): void
    {
        $deps = $this->getDeps(
            new UsesDependencyEmitter(),
            __DIR__.'/Fixtures/Foo.php'
        );

        self::assertCount(1, $deps);
        self::assertContains('Foo\Bar:4 on SomeUse', $deps);
    }
}
