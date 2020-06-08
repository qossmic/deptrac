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

        static::assertSame(
            [
                'Foo\Bar:30 on Foo\SomeParam',
                'Foo\Bar:30 on Foo\SomeClass',
                'Foo\Bar:32 on Foo\SomeClass',
                'Foo\Bar:33 on SomeOtherClass',
                'Foo\Bar:35 on Foo\SomeOtherParam',
                'Foo\Bar:39 on Foo\SomeInstanceOf',
                'Foo\Bar:41 on Foo\SomeClass',
                'Foo\Bar:43 on Foo\SomeClass',
                'Foo\Bar:46 on Some\NamespacedClass',
                'Foo\Bar:58 on Foo\SomeClass',
                'Foo\Bar:66 on Foo\string2',
                'Foo\Bar:78 on Foo\SomeClass',
            ],
            $deps
        );
    }
}
