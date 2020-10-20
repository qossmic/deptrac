<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\Collector\InheritanceLevelCollector;
use SensioLabs\Deptrac\Collector\Registry;

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

        static::assertEquals($expected, $stat);
    }

    public function testType(): void
    {
        static::assertEquals('inheritanceLevel', (new InheritanceLevelCollector())->getType());
    }
}
