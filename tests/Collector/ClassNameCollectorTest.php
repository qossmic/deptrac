<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\Collector\ClassNameCollector;
use SensioLabs\Deptrac\Collector\Registry;

class ClassNameCollectorTest extends TestCase
{
    public function dataProviderSatisfy(): iterable
    {
        yield [['regex' => 'a'], 'foo\bar', true];
        yield [['regex' => 'a'], 'foo\bbr', false];
    }

    public function testType(): void
    {
        static::assertEquals('className', (new ClassNameCollector())->getType());
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, string $className, bool $expected): void
    {
        $stat = (new ClassNameCollector())->satisfy(
            $configuration,
            new AstClassReference(AstMap\ClassLikeName::fromString($className)),
            $this->prophesize(AstMap::class)->reveal(),
            $this->prophesize(Registry::class)->reveal()
        );

        static::assertEquals($expected, $stat);
    }

    public function testWrongRegexParam(): void
    {
        $this->expectException(\LogicException::class);

        (new ClassNameCollector())->satisfy(
            ['Foo' => 'a'],
            new AstClassReference(AstMap\ClassLikeName::fromString('Foo')),
            $this->prophesize(AstMap::class)->reveal(),
            $this->prophesize(Registry::class)->reveal()
        );
    }
}
