<?php

namespace Tests\SensioLabs\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
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
     * @param $pathLevel
     * @param $levelConfig
     * @param $expected
     *
     * @dataProvider dataTests
     */
    public function testSatisfy($pathLevel, $levelConfig, $expected)
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
            $this->prophesize(AstParserInterface::class)->reveal()
        );

        $this->assertEquals($expected, $stat);
    }

    public function testType()
    {
        $this->assertEquals('inheritanceLevel', (new InheritanceLevelCollector())->getType());
    }
}
