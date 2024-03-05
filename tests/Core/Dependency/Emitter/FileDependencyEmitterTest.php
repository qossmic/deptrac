<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Dependency\Emitter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Dependency\Emitter\FileDependencyEmitter;

final class FileDependencyEmitterTest extends TestCase
{
    use EmitterTrait;

    public function testGetName(): void
    {
        self::assertSame('FileDependencyEmitter', (new FileDependencyEmitter())->getName());
    }

    public function testApplyDependencies(): void
    {
        $deps = $this->getEmittedDependencies(
            new FileDependencyEmitter(),
            __DIR__.'/Fixtures/Baz.php'
        );

        self::assertEqualsCanonicalizing([
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:7 on Foo\SomeClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:8 on SomeOtherClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:10 on Foo\SomeOtherParam',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:13 on Foo\SomeInstanceOf',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:15 on Foo\SomeClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:17 on Foo\SomeClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:20 on Foo\SomeClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:25 on Foo\SomeClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:33 on Foo\string2',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:41 on Foo\SomeClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:45 on $_SESSION',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:46 on $_POST',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:54 on Foo\SomeClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:55 on SomeOtherClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:57 on Foo\SomeOtherParam',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:60 on Foo\SomeInstanceOf',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:62 on Foo\SomeClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:64 on Foo\SomeClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:52 on Foo\SomeParam',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:52 on Foo\SomeClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:67 on Some\NamespacedClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:74 on Foo\SomeClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:79 on Foo\SomeClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:87 on Foo\string2',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:95 on Foo\SomeClass',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:110 on $_SESSION',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:111 on $_POST',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:48 on Foo\BarExtends',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:48 on Foo\BarInterface1',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:48 on BarInterface2',
            '/tests/Core/Dependency/Emitter/Fixtures/Baz.php:50 on Foo\SomeTrait',
        ],
            $deps);
    }
}
