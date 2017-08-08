<?php

namespace SensioLabs\Deptrac\Tests\DependencyEmitter;

use SensioLabs\Deptrac\DependencyEmitter\EmittedDependency;
use SensioLabs\Deptrac\DependencyResult\Dependency;

class EmittedDependencyTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $dep = new EmittedDependency('class', 3, Dependency::TYPE_USE);
        $this->assertEquals('class', $dep->getClass());
        $this->assertEquals(3, $dep->getLine());
        $this->assertEquals(Dependency::TYPE_USE, $dep->getType());
    }
}
