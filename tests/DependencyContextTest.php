<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyContext;

class DependencyContextTest extends TestCase
{
    public function testGetSet(): void
    {
        $context = new DependencyContext(
            $astMap = $this->prophesize(AstMap::class)->reveal(),
            [1, 2, 3],
            $dependencyResult = $this->prophesize(Result::class)->reveal(),
            $classNameLayerResolver = $this->prophesize(ClassNameLayerResolverInterface::class)->reveal()
        );

        static::assertSame($astMap, $context->getAstMap());
        static::assertEquals([1, 2, 3], $context->getViolations());
        static::assertSame($dependencyResult, $context->getDependencyResult());
        static::assertSame($classNameLayerResolver, $context->getClassNameLayerResolver());
    }
}
