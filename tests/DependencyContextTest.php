<?php

namespace Tests\SensioLabs\Deptrac;

use PHPUnit\Framework\TestCase;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyContext;

class DependencyContextTest extends TestCase
{
    public function testGetSet()
    {
        $context = new DependencyContext(
            $astMap = $this->prophesize(AstMap::class)->reveal(),
            [1, 2, 3],
            $dependencyResult = $this->prophesize(Result::class)->reveal(),
            $classNameLayerResolver = $this->prophesize(ClassNameLayerResolverInterface::class)->reveal()
        );

        $this->assertSame($astMap, $context->getAstMap());
        $this->assertEquals([1, 2, 3], $context->getViolations());
        $this->assertSame($dependencyResult, $context->getDependencyResult());
        $this->assertSame($classNameLayerResolver, $context->getClassNameLayerResolver());
    }
}
