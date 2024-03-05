<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Dependency\Emitter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Dependency\Emitter\ClassDependencyEmitter;

final class ClassDependencyEmitterTest extends TestCase
{
    use EmitterTrait;

    public function testGetName(): void
    {
        self::assertSame('ClassDependencyEmitter', (new ClassDependencyEmitter())->getName());
    }

    public function testApplyDependencies(): void
    {
        $deps = $this->getEmittedDependencies(
            new ClassDependencyEmitter(),
            __DIR__.'/Fixtures/Foo.php'
        );

        self::assertEqualsCanonicalizing([
            'Foo\Bar:6 on Foo\BarExtends',
            'Foo\Bar:6 on Foo\BarInterface1',
            'Foo\Bar:6 on BarInterface2',
            'Foo\Bar:8 on Foo\SomeTrait',
            'Foo\Bar:10 on Foo\SomeParam',
            'Foo\Bar:10 on Foo\SomeClass',
            'Foo\Bar:12 on Foo\SomeClass',
            'Foo\Bar:13 on SomeOtherClass',
            'Foo\Bar:15 on Foo\SomeOtherParam',
            'Foo\Bar:19 on Foo\SomeInstanceOf',
            'Foo\Bar:21 on Foo\SomeClass',
            'Foo\Bar:23 on Foo\SomeClass',
            'Foo\Bar:26 on Some\NamespacedClass',
            'Foo\Bar:30 on Foo\SomeClass',
            'Foo\Bar:32 on Foo\SomeClass',
            'Foo\Bar:36 on Foo\string2',
            'Foo\Bar:38 on string', // todo: This is a bug in the parser for FQ "\string"
            'Foo\Bar:42 on Foo\SomeClass',
        ], $deps);
    }
}
