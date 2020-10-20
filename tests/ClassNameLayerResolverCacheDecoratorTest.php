<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\ClassNameLayerResolverCacheDecorator;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;

final class ClassNameLayerResolverCacheDecoratorTest extends TestCase
{
    public function testGetLayersByClassName(): void
    {
        $classLikeName = ClassLikeName::fromFQCN('foo');
        $decorated = $this->prophesize(ClassNameLayerResolverInterface::class);
        $decorated->getLayersByClassName($classLikeName)->willReturn(['bar']);

        $decorator = new ClassNameLayerResolverCacheDecorator($decorated->reveal());

        static::assertEquals(['bar'], $decorator->getLayersByClassName($classLikeName));
        static::assertEquals(['bar'], $decorator->getLayersByClassName($classLikeName));
    }
}
