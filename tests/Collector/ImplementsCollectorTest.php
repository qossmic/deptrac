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
        $fooFileReference = new AstMap\AstFileReference('foo.php');
        $fooClassReference = AstMap\ClassReferenceBuilder::create($fooFileReference, 'App\Foo')
            ->implements('App\Bar', 2)
            ->build();

        $barFileReference = new AstMap\AstFileReference('bar.php');
        AstMap\ClassReferenceBuilder::create($barFileReference, 'App\Bar')
            ->implements('App\Baz', 2)
            ->build();

        $bazFileReference = new AstMap\AstFileReference('baz.php');
        AstMap\ClassReferenceBuilder::create($bazFileReference, 'App\Baz')
            ->build();

        $stat = (new ImplementsCollector())->satisfy(
            $configuration,
            $fooClassReference,
            new AstMap([$fooFileReference, $barFileReference, $bazFileReference]),
            $this->createMock(Registry::class)
        );

        static::assertEquals($expected, $stat);
    }
}
