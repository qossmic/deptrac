<?php

namespace Tests\SensioLabs\Deptrac;

use PHPUnit\Framework\TestCase;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\DependencyResult;

class DependencyContextTest extends TestCase
{
    public function testGetSet()
    {
        $context = new DependencyContext(
            $astMap = $this->prophesize(AstMap::class)->reveal(),
            [1, 2, 3],
            $dependencyResult = $this->prophesize(DependencyResult::class)->reveal(),
            $classNameLayerResolver = $this->prophesize(ClassNameLayerResolverInterface::class)->reveal()
        );

        $this->assertSame($astMap, $context->getAstMap());
        $this->assertEquals([1, 2, 3], $context->getViolations());
        $this->assertSame($dependencyResult, $context->getDependencyResult());
        $this->assertSame($classNameLayerResolver, $context->getClassNameLayerResolver());
    }
}
