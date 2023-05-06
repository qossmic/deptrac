<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Layer\Collector;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Layer\InvalidCollectorDefinitionException;
use Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike\FunctionLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike\FunctionLikeToken;
use Qossmic\Deptrac\Core\Layer\Collector\FunctionNameCollector;

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
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, string $functionName, bool $expected): void
    {
        $actual = $this->collector->satisfy(
            $configuration,
            new FunctionLikeReference(FunctionLikeToken::fromFQCN($functionName)),
        );

        self::assertSame($expected, $actual);
    }

    public function testWrongRegexParam(): void
    {
        $this->expectException(InvalidCollectorDefinitionException::class);

        $this->collector->satisfy(
            ['Foo' => 'a'],
            new FunctionLikeReference(FunctionLikeToken::fromFQCN('Foo')),
        );
    }
}
