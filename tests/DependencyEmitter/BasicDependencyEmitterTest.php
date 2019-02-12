<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\DependencyEmitter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\DependencyEmitter\BasicDependencyEmitter;

class BasicDependencyEmitterTest extends TestCase
{
    use EmitterTrait;

    public function testGetName(): void
    {
        static::assertEquals('BasicDependencyEmitter', (new BasicDependencyEmitter())->getName());
    }

    public function testApplyDependencies(): void
    {
        $deps = $this->getDeps(
            new BasicDependencyEmitter(),
            new \SplFileInfo(__DIR__.'/Fixtures/Foo.php')
        );

        static::assertCount(18, $deps);
        static::assertContains('Foo\Bar:4 on SomeUse', $deps);
        static::assertContains('Foo\Bar:10 on Foo\SomeParam', $deps);
        static::assertContains('Foo\Bar:10 on Foo\SomeClass', $deps);
        static::assertContains('Foo\Bar:12 on Foo\SomeClass', $deps);
        static::assertContains('Foo\Bar:13 on SomeOtherClass', $deps);
        static::assertContains('Foo\Bar:15 on Foo\SomeOtherParam', $deps);
        static::assertContains('Foo\Bar:19 on Foo\SomeInstanceOf', $deps);
        static::assertContains('Foo\Bar:21 on Foo\SomeClass', $deps);
        static::assertContains('Foo\Bar:23 on Foo\SomeClass', $deps);
        static::assertContains('Foo\Bar:26 on Some\NamespacedClass', $deps);
        static::assertContains('Foo\Bar:30 on Foo\SomeClass', $deps);
        static::assertContains('Foo\Bar:32 on Foo\SomeClass', $deps);
        static::assertContains('Foo\Bar:36 on Foo\string2', $deps);
        static::assertContains('Foo\Bar:38 on string', $deps);
        static::assertContains('Foo\Bar:40 on string', $deps);
        static::assertContains('Foo\Bar:42 on Foo\SomeClass', $deps);
        static::assertContains('Foo\Bar:44 on self', $deps);
        static::assertContains('Foo\Bar:46 on self', $deps);
    }
}
