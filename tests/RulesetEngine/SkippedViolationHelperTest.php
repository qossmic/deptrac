<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\RulesetEngine;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\Configuration\ConfigurationSkippedViolation;
use Qossmic\Deptrac\RulesetEngine\SkippedViolationHelper;

final class SkippedViolationHelperTest extends TestCase
{
    public function testIsViolationSkipped(): void
    {
        $configuration = ConfigurationSkippedViolation::fromArray(
            [
                'ClassWithOneDep' => [
                    'DependencyClass',
                ],
                'ClassWithEmptyDeps' => [],
                'ClassWithMultipleDeps' => [
                    'DependencyClass1',
                    'DependencyClass2',
                    'DependencyClass2',
                ],
            ]
        );
        $helper = new SkippedViolationHelper($configuration);

        self::assertTrue(
            $helper->isViolationSkipped(
                ClassLikeName::fromFQCN('ClassWithOneDep'),
                ClassLikeName::fromFQCN('DependencyClass')
            )
        );
        self::assertFalse(
            $helper->isViolationSkipped(
                ClassLikeName::fromFQCN('ClassWithEmptyDeps'),
                ClassLikeName::fromFQCN('DependencyClass')
            )
        );
        self::assertTrue(
            $helper->isViolationSkipped(
                ClassLikeName::fromFQCN('ClassWithMultipleDeps'),
                ClassLikeName::fromFQCN('DependencyClass1')
            )
        );
        self::assertTrue(
            $helper->isViolationSkipped(
                ClassLikeName::fromFQCN('ClassWithMultipleDeps'),
                ClassLikeName::fromFQCN('DependencyClass2')
            )
        );
    }

    public function testUnmatchedSkippedViolations(): void
    {
        $configuration = ConfigurationSkippedViolation::fromArray(
            [
                'ClassWithOneDep' => [
                    'DependencyClass',
                ],
                'ClassWithEmptyDeps' => [],
                'ClassWithMultipleDeps' => [
                    'DependencyClass1',
                    'DependencyClass2',
                    'DependencyClass2',
                ],
            ]
        );
        $helper = new SkippedViolationHelper($configuration);

        self::assertTrue(
            $helper->isViolationSkipped(
                ClassLikeName::fromFQCN('ClassWithOneDep'),
                ClassLikeName::fromFQCN('DependencyClass')
            )
        );
        self::assertSame(
            [
                'ClassWithMultipleDeps' => [
                    'DependencyClass1',
                    'DependencyClass2',
                    'DependencyClass2',
                ],
            ],
            $helper->unmatchedSkippedViolations()
        );
    }
}
