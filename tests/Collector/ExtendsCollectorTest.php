<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\Collector\ExtendsCollector;
use Qossmic\Deptrac\Collector\Registry;

final class ExtendsCollectorTest extends TestCase
{
    public function testGetType(): void
    {
        self::assertEquals('extends', (new ExtendsCollector())->getType());
    }

    public function dataProviderSatisfy(): iterable
    {
        yield [['extends' => 'App\FizTrait'], false];
        yield [['extends' => 'App\Bar'], false];
        yield [['extends' => 'App\Baz'], false];
        yield [['extends' => 'App\Foo'], true];
        yield [['extends' => 'App\None'], false];
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, bool $expected): void
    {
        $fooFileReferenceBuilder = AstMap\File\FileReferenceBuilder::create('foo.php');
        $fooFileReferenceBuilder
            ->newClassLike('App\Foo')
            ->implements('App\Bar', 2);
        $fooFileReference = $fooFileReferenceBuilder->build();

        $barFileReferenceBuilder = AstMap\File\FileReferenceBuilder::create('bar.php');
        $barFileReferenceBuilder
            ->newClassLike('App\Bar')
            ->implements('App\Baz', 2);
        $barFileReference = $barFileReferenceBuilder->build();

        $bazFileReferenceBuilder = AstMap\File\FileReferenceBuilder::create('baz.php');
        $bazFileReferenceBuilder->newClassLike('App\Baz');
        $bazFileReference = $bazFileReferenceBuilder->build();

        $fizTraitFileReferenceBuilder = AstMap\File\FileReferenceBuilder::create('fiztrait.php');
        $fizTraitFileReferenceBuilder
            ->newClassLike('App\FizTrait');
        $fizTraitFileReference = $fizTraitFileReferenceBuilder->build();

        $fooBarFileReferenceBuilder = AstMap\File\FileReferenceBuilder::create('foobar.php');
        $fooBarFileReferenceBuilder
            ->newClassLike('App\FooBar')
            ->extends('App\Foo', 2)
            ->trait('App\FizTrait', 4);
        $fooBarFileReference = $fooBarFileReferenceBuilder->build();

        $stat = (new ExtendsCollector())->satisfy(
            $configuration,
            $fooBarFileReference->getAstClassReferences()[0],
            new AstMap([$fooFileReference, $barFileReference, $bazFileReference, $fooBarFileReference, $fizTraitFileReference]),
            $this->createMock(Registry::class)
        );

        self::assertEquals($expected, $stat);
    }
}
