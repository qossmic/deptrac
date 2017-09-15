<?php

namespace Tests\SensioLabs\Deptrac\DependencyResult;

use SensioLabs\Deptrac\DependencyResult\Dependency;

class DependencyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $dependency = new Dependency('a', 23, 'b');
        $this->assertEquals('a', $dependency->getClassA());
        $this->assertEquals(23, $dependency->getClassALine());
        $this->assertEquals('b', $dependency->getClassB());
    }
}
