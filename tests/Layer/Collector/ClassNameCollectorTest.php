<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Layer\Collector;

use LogicException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Layer\Collector\ClassNameCollector;

final class ClassNameCollectorTest extends TestCase
{
    private ClassNameCollector $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collector = new ClassNameCollector();
    }

    public function dataProviderSatisfy(): iterable
    {
        yield [['value' => 'a'], 'foo\bar', true];
        yield [['value' => 'a'], 'foo\bbr', false];
        // Legacy atttribute:
        yield [['regex' => 'a'], 'foo\bar', true];
        yield [['regex' => 'a'], 'foo\bbr', false];
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, string $className, bool $expected): void
    {
        $actual = $this->collector->satisfy(
            $configuration,
            new ClassLikeReference(ClassLikeToken::fromFQCN($className)),
            new AstMap([])
        );

        self::assertSame($expected, $actual);
    }

    public function testWrongRegexParam(): void
    {
        $this->expectException(LogicException::class);

        $this->collector->satisfy(
            ['Foo' => 'a'],
            new ClassLikeReference(ClassLikeToken::fromFQCN('Foo')),
            new AstMap([])
        );
    }

    public function testInvalidRegexParam(): void
    {
        $this->expectException(LogicException::class);

        $this->collector->satisfy(
            ['value' => '/'],
            new ClassLikeReference(ClassLikeToken::fromFQCN('Foo')),
            new AstMap([])
        );
    }
}
