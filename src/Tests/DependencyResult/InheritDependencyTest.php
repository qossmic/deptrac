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
            $astInherit = $this->prophesize(AstInheritInterface::class)->reveal()
        );

        $this->assertEquals('a', $dependency->getClassA());
        $this->assertSame($astInherit, $dependency->getPath());
    }
}
