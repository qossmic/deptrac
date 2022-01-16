<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\DependencyEmitter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\DependencyEmitter\FunctionSuperglobalDependencyEmitter;

final class FunctionSuperglobalDependencyEmitterTest extends TestCase
{
    use EmitterTrait;

    public function testApplyDependencies(): void
    {
        $deps = $this->getDeps(
            new FunctionSuperglobalDependencyEmitter(),
            __DIR__.'/Fixtures/Bar.php'
        );

        self::assertCount(4, $deps);
        self::assertContains('Foo\test():33 on $_SESSION', $deps);
        self::assertContains('Foo\test():34 on $_POST', $deps);
        self::assertContains('Foo\testAnonymousClass():81 on $_SESSION', $deps);
        self::assertContains('Foo\testAnonymousClass():82 on $_POST', $deps);
    }
}
