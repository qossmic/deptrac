<?php

namespace Tests\SensioLabs\Deptrac;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\ClassNameLayerResolverCacheDecorator;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;

class ClassNameLayerResolverCacheDecoratorTest extends TestCase
{
    public function testGetLayersByClassName()
    {
        $decorated = $this->prophesize(ClassNameLayerResolverInterface::class);
        $decorated->getLayersByClassName('foo')->willReturn(['bar']);

        $decorator = new ClassNameLayerResolverCacheDecorator($decorated->reveal());

        $this->assertEquals(['bar'], $decorator->getLayersByClassName('foo'));
        $this->assertEquals(['bar'], $decorator->getLayersByClassName('foo'));
    }
}
