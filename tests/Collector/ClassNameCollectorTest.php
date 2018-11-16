<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParserInterface;
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
        $astClassReference = $this->prophesize(AstClassReferenceInterface::class);
        $astClassReference->getClassName()->willReturn($className);

        $stat = (new ClassNameCollector())->satisfy(
            $configuration,
            $astClassReference->reveal(),
            $this->prophesize(AstMap::class)->reveal(),
            $this->prophesize(Registry::class)->reveal(),
            $this->prophesize(AstParserInterface::class)->reveal()
        );

        static::assertEquals($expected, $stat);
    }

    /**
     * @expectedException \LogicException
     */
    public function testWrongRegexParam(): void
    {
        (new ClassNameCollector())->satisfy(
            ['Foo' => 'a'],
            $this->prophesize(AstClassReferenceInterface::class)->reveal(),
            $this->prophesize(AstMap::class)->reveal(),
            $this->prophesize(Registry::class)->reveal(),
            $this->prophesize(AstParserInterface::class)->reveal()
        );
    }
}
