<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Dependency\Emitter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Dependency\Emitter\UseDependencyEmitter;

final class UsesDependencyEmitterTest extends TestCase
{
    use EmitterTrait;

    public function testApplyDependencies(): void
    {
        $deps = $this->getDeps(
            new UseDependencyEmitter(),
            __DIR__.'/Fixtures/Foo.php'
        );

        self::assertCount(1, $deps);
        self::assertContains('Foo\Bar:4 on SomeUse', $deps);
    }

    public function testIgnoresNamespaces(): void
    {
        $deps = $this->getDeps(
            new UseDependencyEmitter(),
            [
                __DIR__.'/Fixtures/IgnoreNamespace/Deps/UsedWithFQDN.php',
                __DIR__.'/Fixtures/IgnoreNamespace/Deps/UsedWithNamespace.php',
                __DIR__.'/Fixtures/IgnoreNamespace/Deps/Functions.php',
                __DIR__.'/Fixtures/IgnoreNamespace/Uses/Foo.php',
            ]
        );

        self::assertCount(2, $deps);
        self::assertNotContains('IgnoreNamespace\Uses\Foo:5 on IgnoreNamespace\Deps', $deps);
        self::assertContains('IgnoreNamespace\Uses\Foo:6 on IgnoreNamespace\Deps\UsedWithFQDN', $deps);
        self::assertContains('IgnoreNamespace\Uses\Foo:7 on IgnoreNamespace\Deps\Functions\functionUsedWithFQDN', $deps);
    }

    public function testIncludesFQDNWhichIsAlsoANamespacePrefix(): void
    {
        $deps = $this->getDeps(
            new UseDependencyEmitter(),
            [
                __DIR__.'/Fixtures/FQDNNamespacePrefix/FQDN.php',
                __DIR__.'/Fixtures/FQDNNamespacePrefix/FQDN/SomeClass.php',
                __DIR__.'/Fixtures/FQDNNamespacePrefix/Uses/Foo.php',
            ]
        );

        self::assertCount(1, $deps);
        self::assertContains('FQDNNamespacePrefix\Uses\Foo:5 on FQDNNamespacePrefix\FQDN', $deps);
    }
}
