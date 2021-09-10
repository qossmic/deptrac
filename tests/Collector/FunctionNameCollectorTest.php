<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\Collector\FunctionNameCollector;
use Qossmic\Deptrac\Collector\Registry;

final class FunctionNameCollectorTest extends TestCase
{
    public function dataProviderSatisfy(): iterable
    {
        yield [['regex' => 'a'], 'foo\bar', true];
        yield [['regex' => 'a'], 'foo\bbr', false];
    }

    public function testType(): void
    {
        self::assertEquals('functionName', (new FunctionNameCollector())->getType());
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, string $functionName, bool $expected): void
    {
        $stat = (new FunctionNameCollector())->satisfy(
            $configuration,
            new AstMap\AstFunctionReference(AstMap\FunctionName::fromFQCN($functionName)),
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );

        self::assertEquals($expected, $stat);
    }

    public function testWrongRegexParam(): void
    {
        $this->expectException(\LogicException::class);

        (new FunctionNameCollector())->satisfy(
            ['Foo' => 'a'],
            new AstMap\AstFunctionReference(AstMap\FunctionName::fromFQCN('Foo')),
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );
    }
}
