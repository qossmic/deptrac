<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\ClassNameLayerResolverCacheDecorator;
use Qossmic\Deptrac\ClassNameLayerResolverInterface;

final class ClassNameLayerResolverCacheDecoratorTest extends TestCase
{
    public function testGetLayersByClassName(): void
    {
        $classLikeName = ClassLikeName::fromFQCN('foo');
        $decorated = $this->prophesize(ClassNameLayerResolverInterface::class);
        $decorated->getLayersByClassName($classLikeName)->willReturn(['bar']);

        $decorator = new ClassNameLayerResolverCacheDecorator($decorated->reveal());

        self::assertEquals(['bar'], $decorator->getLayersByClassName($classLikeName));
        self::assertEquals(['bar'], $decorator->getLayersByClassName($classLikeName));
    }
}
