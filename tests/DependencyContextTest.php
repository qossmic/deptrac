<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;

class DependencyContextTest extends TestCase
{
    public function testGetSet(): void
    {
        $context = new DependencyContext(
            $astMap = $this->prophesize(AstMap::class)->reveal(),
            $dependencyResult = $this->prophesize(Result::class)->reveal(),
            $classNameLayerResolver = $this->prophesize(ClassNameLayerResolverInterface::class)->reveal(),
            [1, 2, 3]
        );

        static::assertSame($astMap, $context->getAstMap());
        static::assertEquals([1, 2, 3], $context->getViolations());
        static::assertSame($dependencyResult, $context->getDependencyResult());
        static::assertSame($classNameLayerResolver, $context->getClassNameLayerResolver());
    }

    public function testIsViolationSkipped(): void
    {
        $skippedViolations = [
            new RulesetViolation(
                new Dependency('ClassA', 12, 'ClassB'),
                'LayerA',
                'LayerB'
            ),
        ];
        $context = new DependencyContext(
            $astMap = $this->prophesize(AstMap::class)->reveal(),
            $dependencyResult = $this->prophesize(Result::class)->reveal(),
            $classNameLayerResolver = $this->prophesize(ClassNameLayerResolverInterface::class)->reveal(),
            [],
            $skippedViolations
        );
        $this->assertSame($skippedViolations, $context->getSkippedViolations());
    }

    public function testGetSkippedViolationsByLayerName(): void
    {
        $skippedViolations = [
            new RulesetViolation(
                new Dependency('ClassA', 12, 'ClassB'),
                'LayerA',
                'LayerB'
            ),
            $matchedViolation = new RulesetViolation(
                new Dependency('ClassA', 12, 'ClassB'),
                'LayerC',
                'LayerD'
            ),
        ];
        $context = new DependencyContext(
            $astMap = $this->prophesize(AstMap::class)->reveal(),
            $dependencyResult = $this->prophesize(Result::class)->reveal(),
            $classNameLayerResolver = $this->prophesize(ClassNameLayerResolverInterface::class)->reveal(),
            [],
            $skippedViolations
        );
        $this->assertSame([$matchedViolation], $context->getSkippedViolationsByLayerName('LayerC'));
        $this->assertSame([], $context->getSkippedViolationsByLayerName('LayerB'));
    }

    public function testGetSkippedViolations(): void
    {
        $skippedViolations = [
            new RulesetViolation(
                new Dependency('ClassA', 12, 'ClassB'),
                'LayerA',
                'LayerB'
            ),
            $matchedViolation = new RulesetViolation(
                new Dependency('ClassA', 12, 'ClassB'),
                'LayerC',
                'LayerD'
            ),
        ];
        $context = new DependencyContext(
            $astMap = $this->prophesize(AstMap::class)->reveal(),
            $dependencyResult = $this->prophesize(Result::class)->reveal(),
            $classNameLayerResolver = $this->prophesize(ClassNameLayerResolverInterface::class)->reveal(),
            [],
            $skippedViolations
        );
        $this->assertSame($skippedViolations, $context->getSkippedViolations());
    }
}
