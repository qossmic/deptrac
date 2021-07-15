<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\RulesetEngine;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap\ClassToken\ClassLikeName;
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
                ClassLikeName::fromFQCN('ClassWithOneDep')->toString(),
                ClassLikeName::fromFQCN('DependencyClass')->toString()
            )
        );
        self::assertFalse(
            $helper->isViolationSkipped(
                ClassLikeName::fromFQCN('ClassWithEmptyDeps')->toString(),
                ClassLikeName::fromFQCN('DependencyClass')->toString()
            )
        );
        self::assertTrue(
            $helper->isViolationSkipped(
                ClassLikeName::fromFQCN('ClassWithMultipleDeps')->toString(),
                ClassLikeName::fromFQCN('DependencyClass1')->toString()
            )
        );
        self::assertTrue(
            $helper->isViolationSkipped(
                ClassLikeName::fromFQCN('ClassWithMultipleDeps')->toString(),
                ClassLikeName::fromFQCN('DependencyClass2')->toString()
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
                ClassLikeName::fromFQCN('ClassWithOneDep')->toString(),
                ClassLikeName::fromFQCN('DependencyClass')->toString()
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
