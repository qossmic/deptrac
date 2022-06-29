<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Layer\Collector;

use LogicException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Layer\Collector\ClassNameRegexCollector;

final class ClassNameRegexCollectorTest extends TestCase
{
    private ClassNameRegexCollector $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collector = new ClassNameRegexCollector();
    }

    public function dataProviderSatisfy(): iterable
    {
        yield [['value' => '/^Foo\\\\Bar$/i'], 'Foo\\Bar', true];
        yield [['value' => '/^Foo\\\\Bar$/i'], 'Foo\\Baz', false];
        // Legacy attribute:
        yield [['regex' => '/^Foo\\\\Bar$/i'], 'Foo\\Bar', true];
        yield [['regex' => '/^Foo\\\\Bar$/i'], 'Foo\\Baz', false];
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
            ['regex' => '/'],
            new ClassLikeReference(ClassLikeToken::fromFQCN('Foo')),
            new AstMap([])
        );
    }
}
