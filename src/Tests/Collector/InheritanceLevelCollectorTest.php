<?php

namespace SensioLabs\Deptrac\Tests\Collector;

use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\Collector\InheritanceLevelCollector;
use SensioLabs\Deptrac\CollectorFactory;
use Prophecy\Argument;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;

class InheritanceLevelCollectorTest extends  \PHPUnit_Framework_TestCase
{
    public function dataTests()
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
            ->willReturn(array_fill(0, $pathLevel, 1))
        ;

        $astMap = $this->prophesize(AstMap::class);
        $astMap->getClassInherits(Argument::any())
            ->willReturn([$classInherit->reveal()])
        ;

        $stat = (new InheritanceLevelCollector())
            ->satisfy(
                ['level' => $levelConfig],
                $this->prophesize(AstClassReferenceInterface::class)->reveal(),
                $astMap->reveal(),
                $this->prophesize(CollectorFactory::class)->reveal(),
                $this->prophesize(AstParserInterface::class)->reveal()
            )
        ;

        $this->assertEquals($expected, $stat);
    }

    public function testType()
    {
        $this->assertEquals('inheritanceLevel', (new InheritanceLevelCollector())->getType());
    }
}
