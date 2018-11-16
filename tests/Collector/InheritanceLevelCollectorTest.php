<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\Collector\Registry;
use SensioLabs\Deptrac\Collector\InheritanceLevelCollector;

class InheritanceLevelCollectorTest extends TestCase
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
        $classInherit = $this->prophesize(AstMap\AstInheritInterface::class);
        $classInherit->getPath()
            ->willReturn(array_fill(0, $pathLevel, 1));

        $astMap = $this->prophesize(AstMap::class);
        $astMap->getClassInherits(AstMap\AstInheritInterface::class)
            ->willReturn([$classInherit->reveal()]);

        $classReference = $this->prophesize(AstClassReferenceInterface::class);
        $classReference->getClassName()
            ->willReturn(AstMap\AstInheritInterface::class);

        $stat = (new InheritanceLevelCollector())->satisfy(
            ['level' => $levelConfig],
            $classReference->reveal(),
            $astMap->reveal(),
            $this->prophesize(Registry::class)->reveal(),
            $this->prophesize(AstParserInterface::class)->reveal()
        );

        static::assertEquals($expected, $stat);
    }

    public function testType(): void
    {
        static::assertEquals('inheritanceLevel', (new InheritanceLevelCollector())->getType());
    }
}
