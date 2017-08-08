<?php

namespace SensioLabs\Deptrac\Tests\DependencyResult;

use SensioLabs\Deptrac\DependencyResult\Dependency;

class DependencyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $dependency = new Dependency('a', 23, 'b', Dependency::TYPE_STATIC_METHOD);
        $this->assertEquals('a', $dependency->getClassA());
        $this->assertEquals(23, $dependency->getClassALine());
        $this->assertEquals('b', $dependency->getClassB());
        $this->assertEquals(Dependency::TYPE_STATIC_METHOD, $dependency->getType());
    }

    public function testImplicitType()
    {
        $dependency = new Dependency('a', 23, 'b');
        $this->assertEquals(Dependency::TYPE_USE, $dependency->getType());
    }
}
