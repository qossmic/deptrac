<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\Collector\ClassNameRegexCollector;
use SensioLabs\Deptrac\Collector\Registry;

final class ClassNameRegexCollectorTest extends TestCase
{
    public function dataProviderSatisfy(): iterable
    {
        yield [['regex' => '/^Foo\\\\Bar$/i'], 'Foo\\Bar', true];
        yield [['regex' => '/^Foo\\\\Bar$/i'], 'Foo\\Baz', false];
    }

    public function testType(): void
    {
        static::assertEquals('classNameRegex', (new ClassNameRegexCollector())->getType());
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, string $className, bool $expected): void
    {
        $stat = (new ClassNameRegexCollector())->satisfy(
            $configuration,
            new AstClassReference(ClassLikeName::fromFQCN($className)),
            $this->prophesize(AstMap::class)->reveal(),
            $this->prophesize(Registry::class)->reveal()
        );

        static::assertEquals($expected, $stat);
    }

    public function testWrongRegexParam(): void
    {
        $this->expectException(\LogicException::class);

        (new ClassNameRegexCollector())->satisfy(
            ['Foo' => 'a'],
            new AstClassReference(ClassLikeName::fromFQCN('Foo')),
            $this->prophesize(AstMap::class)->reveal(),
            $this->prophesize(Registry::class)->reveal()
        );
    }
}
