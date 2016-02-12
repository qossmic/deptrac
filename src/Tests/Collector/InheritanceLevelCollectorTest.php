<?php

namespace DependencyTracker\Tests\Collector;

use DependencyTracker\Collector\InheritanceLevelCollector;
use DependencyTracker\CollectorFactory;
use Prophecy\Argument;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;

class InheritanceLevelCollectorTest extends  \PHPUnit_Framework_TestCase
{
    public function testSatisfy()
    {
        $classInherit = $this->prophesize(AstMap\AstInheritInterface::class);
        $classInherit->getPath()
            ->willReturn([1, 1])
        ;

        $astMap = $this->prophesize(AstMap::class);
        $astMap->getClassInherits(Argument::any())
            ->willReturn([$classInherit->reveal()])
        ;

        $stat = (new InheritanceLevelCollector())
            ->satisfy(
                ['level' => 1],
                $this->prophesize(AstClassReferenceInterface::class)->reveal(),
                $astMap->reveal(),
                $this->prophesize(CollectorFactory::class)->reveal()
            )
        ;

        $this->assertTrue($stat);
    }

    public function testNegativeSatisfy()
    {
        $classInherit = $this->prophesize(AstMap\AstInheritInterface::class);
        $classInherit->getPath()
            ->willReturn([])
        ;

        $astMap = $this->prophesize(AstMap::class);
        $astMap->getClassInherits(Argument::any())
            ->willReturn([$classInherit->reveal()])
        ;

        $stat = (new InheritanceLevelCollector())
            ->satisfy(
                ['level' => 1],
                $this->prophesize(AstClassReferenceInterface::class)->reveal(),
                $astMap->reveal(),
                $this->prophesize(CollectorFactory::class)->reveal()
            )
        ;

        $this->assertFalse($stat);
    }
}
