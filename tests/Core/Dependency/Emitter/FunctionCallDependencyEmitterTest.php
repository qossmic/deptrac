<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Dependency\Emitter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Dependency\Emitter\FunctionCallDependencyEmitter;

final class FunctionCallDependencyEmitterTest extends TestCase
{
    use EmitterTrait;

    public function testGetName(): void
    {
        self::assertSame('FunctionCallDependencyEmitter', (new FunctionCallDependencyEmitter())->getName());
    }

    public function testApplyDependencies(): void
    {
        $deps = $this->getEmittedDependencies(
            new FunctionCallDependencyEmitter(),
            __DIR__.'/Fixtures/Bar.php'
        );

        self::assertCount(1, $deps);

        self::assertContains('Foo\testAnonymousClass():86 on Foo\test()', $deps);
    }
}
