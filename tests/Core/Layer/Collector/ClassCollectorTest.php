<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Layer\Collector;

use LogicException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType;
use Qossmic\Deptrac\Core\Layer\Collector\ClassCollector;

final class ClassCollectorTest extends TestCase
{
    private ClassCollector $sut;

    public function setUp(): void
    {
        $this->sut = new ClassCollector();
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
            new ClassLikeReference(ClassLikeToken::fromFQCN($className), ClassLikeType::TYPE_CLASS),
        );

        self::assertSame($expected, $stat);
    }

    public function provideTypes(): iterable
    {
        yield 'classLike' => [ClassLikeType::TYPE_CLASSLIKE, false];
        yield 'class' => [ClassLikeType::TYPE_CLASS, true];
        yield 'interface' => [ClassLikeType::TYPE_INTERFACE, false];
        yield 'trait' => [ClassLikeType::TYPE_TRAIT, false];
    }

    /**
     * @dataProvider provideTypes
     */
    public function testSatisfyTypes(ClassLikeType $classLikeType, bool $matches): void
    {
        $stat = $this->sut->satisfy(
            ['value' => '^Foo\\\\Bar$'],
            new ClassLikeReference(ClassLikeToken::fromFQCN('Foo\\Bar'), $classLikeType),
        );

        self::assertSame($matches, $stat);
    }

    public function testWrongRegexParam(): void
    {
        $this->expectException(LogicException::class);

        $this->sut->satisfy(
            ['Foo' => 'a'],
            new ClassLikeReference(ClassLikeToken::fromFQCN('Foo'), ClassLikeType::TYPE_CLASS),
        );
    }
}
