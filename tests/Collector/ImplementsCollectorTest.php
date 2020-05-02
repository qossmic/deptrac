<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\Collector\ImplementsCollector;
use SensioLabs\Deptrac\Collector\Registry;

class ImplementsCollectorTest extends TestCase
{
    public function testGetType(): void
    {
        static::assertEquals('implements', (new ImplementsCollector())->getType());
    }

    public function dataProviderSatisfy(): iterable
    {
        yield [['implements' => 'App\Bar'], true];
        yield [['implements' => 'App\Baz'], true];
        yield [['implements' => 'App\None'], false];
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, bool $expected): void
    {
        $fooFileReferenceBuilder = AstMap\FileReferenceBuilder::create('foo.php');
        $fooFileReferenceBuilder
            ->newClassLike('App\Foo')
            ->implements('App\Bar', 2);
        $fooFileReference = $fooFileReferenceBuilder->build();

        $barFileReferenceBuilder = AstMap\FileReferenceBuilder::create('bar.php');
        $barFileReferenceBuilder
            ->newClassLike('App\Bar')
            ->implements('App\Baz', 2);
        $barFileReference = $barFileReferenceBuilder->build();

        $bazFileReferenceBuilder = AstMap\FileReferenceBuilder::create('baz.php');
        $bazFileReferenceBuilder->newClassLike('App\Baz');
        $bazFileReference = $bazFileReferenceBuilder->build();

        $stat = (new ImplementsCollector())->satisfy(
            $configuration,
            $fooFileReference->getAstClassReferences()[0],
            new AstMap([$fooFileReference, $barFileReference, $bazFileReference]),
            $this->createMock(Registry::class)
        );

        static::assertEquals($expected, $stat);
    }
}
