<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;
use Qossmic\Deptrac\AstRunner\AstMap\AstInherit;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\Collector\InheritanceLevelCollector;
use Qossmic\Deptrac\Collector\Registry;

final class InheritanceLevelCollectorTest extends TestCase
{
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
        $classInherit = $this->prophesize(AstInherit::class);
        $classInherit->getPath()
            ->willReturn(array_fill(0, $pathLevel, 1));

        $astMap = $this->prophesize(AstMap::class);
        $astMap->getClassInherits(ClassLikeName::fromFQCN(AstInherit::class))
            ->willReturn([$classInherit->reveal()]);

        $stat = (new InheritanceLevelCollector())->satisfy(
            ['level' => $levelConfig],
            new AstClassReference(ClassLikeName::fromFQCN(AstInherit::class)),
            $astMap->reveal(),
            $this->prophesize(Registry::class)->reveal()
        );

        self::assertEquals($expected, $stat);
    }

    public function testType(): void
    {
        self::assertEquals('inheritanceLevel', (new InheritanceLevelCollector())->getType());
    }
}
