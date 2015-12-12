<?php


namespace DependencyTracker\Tests\DependencyResult;


use DependencyTracker\DependencyResult\InheritDependency;
use SensioLabs\AstRunner\AstMap\AstInheritInterface;

class InheritDependencyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $dependency = new InheritDependency(
            'a',
            23,
            $astInherit = $this->prophesize(AstInheritInterface::class)->reveal()
        );

        $this->assertEquals('a', $dependency->getClassA());
        $this->assertEquals(23, $dependency->getClassALine());
        $this->assertSame($astInherit, $dependency->getPath());
    }
}
