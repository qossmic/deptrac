<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Dependency\Emitter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Dependency\Emitter\FunctionDependencyEmitter;

final class FunctionDependencyEmitterTest extends TestCase
{
    use EmitterTrait;

    public function testGetName(): void
    {
        self::assertSame('FunctionDependencyEmitter', (new FunctionDependencyEmitter())->getName());
    }

    public function testApplyDependencies(): void
    {
        $deps = $this->getEmittedDependencies(
            new FunctionDependencyEmitter(),
            __DIR__.'/Fixtures/Bar.php'
        );
        
        self::assertEqualsCanonicalizing([
                                             'Foo\test():6 on Foo\SomeParam',
                                             'Foo\test():6 on Foo\SomeClass',
                                             'Foo\test():8 on Foo\SomeClass',
                                             'Foo\test():9 on SomeOtherClass',
                                             'Foo\test():11 on Foo\SomeOtherParam',
                                             'Foo\test():15 on Foo\SomeInstanceOf',
                                             'Foo\test():17 on Foo\SomeClass',
                                             'Foo\test():19 on Foo\SomeClass',
                                             'Foo\test():21 on Foo\SomeClass',
                                             'Foo\test():23 on Foo\SomeClass',
                                             'Foo\test():27 on Foo\string2',
                                             'Foo\test():31 on Foo\SomeClass',
                                             'Foo\testAnonymousClass():38 on Foo\BarExtends',
                                             'Foo\testAnonymousClass():38 on Foo\BarInterface1',
                                             'Foo\testAnonymousClass():38 on BarInterface2',
                                             'Foo\testAnonymousClass():40 on Foo\SomeTrait',
                                             'Foo\testAnonymousClass():42 on Foo\SomeParam',
                                             'Foo\testAnonymousClass():42 on Foo\SomeClass',
                                             'Foo\testAnonymousClass():44 on Foo\SomeClass',
                                             'Foo\testAnonymousClass():45 on SomeOtherClass',
                                             'Foo\testAnonymousClass():47 on Foo\SomeOtherParam',
                                             'Foo\testAnonymousClass():51 on Foo\SomeInstanceOf',
                                             'Foo\testAnonymousClass():53 on Foo\SomeClass',
                                             'Foo\testAnonymousClass():55 on Foo\SomeClass',
                                             'Foo\testAnonymousClass():58 on Some\NamespacedClass',
                                             'Foo\testAnonymousClass():62 on Foo\SomeClass',
                                             'Foo\testAnonymousClass():64 on Foo\SomeClass',
                                             'Foo\testAnonymousClass():68 on Foo\string2',
                                             'Foo\testAnonymousClass():72 on Foo\SomeClass',
                                         ], $deps);
        self::assertCount(29, $deps);
    }
}
