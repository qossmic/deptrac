<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\DependencyEmitter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\DependencyEmitter\ClassSuperglobalDependencyEmitter;

final class ClassSuperglobalDependencyEmitterTest extends TestCase
{
    use EmitterTrait;

    public function testGetName(): void
    {
        self::assertEquals('ClassSuperglobalDependencyEmitter', (new ClassSuperglobalDependencyEmitter())->getName());
    }

    public function testApplyDependencies(): void
    {
        $deps = $this->getDeps(
            new ClassSuperglobalDependencyEmitter(),
            __DIR__.'/Fixtures/Foo.php'
        );

        self::assertCount(2, $deps);
        self::assertContains('Foo\Bar:51 on $_SESSION', $deps);
        self::assertContains('Foo\Bar:52 on $_POST', $deps);
    }
}
