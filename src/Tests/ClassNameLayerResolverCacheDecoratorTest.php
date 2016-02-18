<?php

namespace DependencyTracker\Tests;

use DependencyTracker\ClassNameLayerResolverCacheDecorator;
use DependencyTracker\ClassNameLayerResolverInterface;

class ClassNameLayerResolverCacheDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLayersByClassName()
    {
        $decorated = $this->prophesize(ClassNameLayerResolverInterface::class);
        $decorated->getLayersByClassName('foo')->willReturn('bar');

        $decorator = new ClassNameLayerResolverCacheDecorator($decorated->reveal());

        $this->assertEquals('bar', $decorator->getLayersByClassName('foo'));
        $this->assertEquals('bar', $decorator->getLayersByClassName('foo'));
    }
}
