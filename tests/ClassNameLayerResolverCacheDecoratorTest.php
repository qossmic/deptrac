<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\ClassNameLayerResolverCacheDecorator;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;

class ClassNameLayerResolverCacheDecoratorTest extends TestCase
{
    public function testGetLayersByClassName(): void
    {
        $decorated = $this->prophesize(ClassNameLayerResolverInterface::class);
        $decorated->getLayersByClassName('foo')->willReturn(['bar']);

        $decorator = new ClassNameLayerResolverCacheDecorator($decorated->reveal());

        static::assertEquals(['bar'], $decorator->getLayersByClassName('foo'));
        static::assertEquals(['bar'], $decorator->getLayersByClassName('foo'));
    }
}
