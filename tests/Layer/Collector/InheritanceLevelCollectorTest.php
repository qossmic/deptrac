<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Layer\Collector;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Layer\Collector\InheritanceLevelCollector;

final class InheritanceLevelCollectorTest extends TestCase
{
    private InheritanceLevelCollector $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collector = new InheritanceLevelCollector();
    }

    public function dataTests(): array
    {
        return [
            [1, 1, true],
            [2, 1, true],
            [3, 2, true],
            [1, 2, false],
            [2, 3, false],
            [3, 4, false],
        ];
    }

    /**
     * @dataProvider dataTests
     */
    public function testSatisfy(int $pathLevel, int $levelConfig, bool $expected): void
    {
        $classInherit = $this->createMock(AstInherit::class);
        $classInherit->method('getPath')
            ->willReturn(array_fill(0, $pathLevel, 1));

        $astMap = $this->createMock(AstMap::class);
        $astMap->method('getClassInherits')
            ->with(ClassLikeToken::fromFQCN(AstInherit::class))
            ->willReturn([$classInherit]);

        $actual = $this->collector->satisfy(
            ['value' => $levelConfig],
            new ClassLikeReference(ClassLikeToken::fromFQCN(AstInherit::class)),
            $astMap,
        );

        self::assertSame($expected, $actual);
    }
}
