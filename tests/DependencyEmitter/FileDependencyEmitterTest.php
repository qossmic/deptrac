<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\DependencyEmitter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\DependencyEmitter\FileDependencyEmitter;

final class FileDependencyEmitterTest extends TestCase
{
    use EmitterTrait;

    public function testGetName(): void
    {
        self::assertEquals('FileDependencyEmitter', (new FileDependencyEmitter())->getName());
    }

    public function testApplyDependencies(): void
    {
        $deps = $this->getDeps(
            new FileDependencyEmitter(),
            __DIR__.'/Fixtures/Baz.php'
        );

        self::assertCount(31, $deps);
        //TODO: Add the actual instances
    }
}
