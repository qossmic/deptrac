<?php

namespace SensioLabs\Deptrac\Tests\DependencyResult;

use SensioLabs\Deptrac\DependencyResult\DependencyInterface;
use SensioLabs\Deptrac\DependencyResult\InheritDependency;
use SensioLabs\AstRunner\AstMap\AstInheritInterface;

class InheritDependencyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $dependency = new InheritDependency(
            'a',
            'b',
            $dep = $this->prophesize(DependencyInterface::class)->reveal(),
            $astInherit = $this->prophesize(AstInheritInterface::class)->reveal()
        );

        $this->assertEquals('a', $dependency->getClassA());
        $this->assertEquals('b', $dependency->getClassB());
        $this->assertEquals('', $dependency->getClassALine());
        $this->assertEquals($dep, $dependency->getOriginalDependency());
        $this->assertSame($astInherit, $dependency->getPath());
    }
}
