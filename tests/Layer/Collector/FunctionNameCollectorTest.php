<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Layer\Collector;

use LogicException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\FunctionLike\FunctionLikeReference;
use Qossmic\Deptrac\Ast\AstMap\FunctionLike\FunctionLikeToken;
use Qossmic\Deptrac\Layer\Collector\FunctionNameCollector;

final class FunctionNameCollectorTest extends TestCase
{
    private FunctionNameCollector $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collector = new FunctionNameCollector();
    }

    public function dataProviderSatisfy(): iterable
    {
        yield [['value' => 'a'], 'foo\bar', true];
        yield [['value' => 'a'], 'foo\bbr', false];
        // Legacy attribute:
        yield [['regex' => 'a'], 'foo\bar', true];
        yield [['regex' => 'a'], 'foo\bbr', false];
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, string $functionName, bool $expected): void
    {
        $actual = $this->collector->satisfy(
            $configuration,
            new FunctionLikeReference(FunctionLikeToken::fromFQCN($functionName)),
            new AstMap([])
        );

        self::assertSame($expected, $actual);
    }

    public function testWrongRegexParam(): void
    {
        $this->expectException(LogicException::class);

        $this->collector->satisfy(
            ['Foo' => 'a'],
            new FunctionLikeReference(FunctionLikeToken::fromFQCN('Foo')),
            new AstMap([])
        );
    }
}
