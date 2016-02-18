<?php

namespace DependencyTracker\Tests\DependencyEmitter;

use DependencyTracker\DependencyEmitter\EmittedDependency;

class EmittedDependencyTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $dep = new EmittedDependency('class', 3, 2);
        $this->assertEquals('class', $dep->getClass());
        $this->assertEquals(3, $dep->getLine());
        $this->assertEquals(2, $dep->getType());
    }
}
