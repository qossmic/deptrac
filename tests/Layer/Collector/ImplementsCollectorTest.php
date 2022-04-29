<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Layer\Collector;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\File\FileReferenceBuilder;
use Qossmic\Deptrac\Layer\Collector\ImplementsCollector;

final class ImplementsCollectorTest extends TestCase
{
    private ImplementsCollector $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collector = new ImplementsCollector();
    }

    public function dataProviderSatisfy(): iterable
    {
        yield [['value' => 'App\FizTrait'], false];
        yield [['value' => 'App\Bar'], true];
        yield [['value' => 'App\Baz'], true];
        yield [['value' => 'App\Foo'], false];
        yield [['value' => 'App\None'], false];
        // Legacy attribute:
        yield [['implements' => 'App\FizTrait'], false];
        yield [['implements' => 'App\Bar'], true];
        yield [['implements' => 'App\Baz'], true];
        yield [['implements' => 'App\Foo'], false];
        yield [['implements' => 'App\None'], false];
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, bool $expected): void
    {
        $fooFileReferenceBuilder = FileReferenceBuilder::create('foo.php');
        $fooFileReferenceBuilder
            ->newClassLike('App\Foo')
            ->implements('App\Bar', 2);
        $fooFileReference = $fooFileReferenceBuilder->build();

        $barFileReferenceBuilder = FileReferenceBuilder::create('bar.php');
        $barFileReferenceBuilder
            ->newClassLike('App\Bar')
            ->implements('App\Baz', 2);
        $barFileReference = $barFileReferenceBuilder->build();

        $bazFileReferenceBuilder = FileReferenceBuilder::create('baz.php');
        $bazFileReferenceBuilder->newClassLike('App\Baz');
        $bazFileReference = $bazFileReferenceBuilder->build();

        $fizTraitFileReferenceBuilder = FileReferenceBuilder::create('fiztrait.php');
        $fizTraitFileReferenceBuilder
            ->newClassLike('App\FizTrait');
        $fizTraitFileReference = $fizTraitFileReferenceBuilder->build();

        $fooBarFileReferenceBuilder = FileReferenceBuilder::create('foobar.php');
        $fooBarFileReferenceBuilder
            ->newClassLike('App\FooBar')
            ->extends('App\Foo', 2)
            ->trait('App\FizTrait', 4);
        $fooBarFileReference = $fooBarFileReferenceBuilder->build();

        $actual = $this->collector->satisfy(
            $configuration,
            $fooBarFileReference->getClassLikeReferences()[0],
            new AstMap([$fooFileReference, $barFileReference, $bazFileReference, $fooBarFileReference, $fizTraitFileReference]),
        );

        self::assertSame($expected, $actual);
    }
}
