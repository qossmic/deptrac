<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Layer\Collector;

use LogicException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType;
use Qossmic\Deptrac\Core\Layer\Collector\TraitCollector;

final class TraitCollectorTest extends TestCase
{
    private TraitCollector $sut;

    public function setUp(): void
    {
        $this->sut = new TraitCollector();
    }

    public function dataProviderSatisfy(): iterable
    {
        yield [['value' => '^Foo\\\\Bar$'], 'Foo\\Bar', true];
        yield [['value' => '^Foo\\\\Bar$'], 'Foo\\Baz', false];
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, string $className, bool $expected): void
    {
        $stat = $this->sut->satisfy(
            $configuration,
            new ClassLikeReference(ClassLikeToken::fromFQCN($className), ClassLikeType::trait()),
            $this->createMock(AstMap::class),
        );

        self::assertEquals($expected, $stat);
    }

    public function provideTypes(): iterable
    {
        yield 'classLike' => [ClassLikeType::classLike(), false];
        yield 'class' => [ClassLikeType::class(), false];
        yield 'interface' => [ClassLikeType::interface(), false];
        yield 'trait' => [ClassLikeType::trait(), true];
    }

    /**
     * @dataProvider provideTypes
     */
    public function testSatisfyTypes(ClassLikeType $classLikeType, bool $matches): void
    {
        $stat = $this->sut->satisfy(
            ['value' => '^Foo\\\\Bar$'],
            new ClassLikeReference(ClassLikeToken::fromFQCN('Foo\\Bar'), $classLikeType),
            $this->createMock(AstMap::class),
        );

        self::assertSame($matches, $stat);
    }

    public function testWrongRegexParam(): void
    {
        $this->expectException(LogicException::class);

        $this->sut->satisfy(
            ['Foo' => 'a'],
            new ClassLikeReference(ClassLikeToken::fromFQCN('Foo'), ClassLikeType::trait()),
            $this->createMock(AstMap::class),
        );
    }
}
