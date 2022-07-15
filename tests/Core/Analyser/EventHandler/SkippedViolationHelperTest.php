<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Analyser\EventHandler;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Analyser\EventHandler\SkippedViolationHelper;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;

final class SkippedViolationHelperTest extends TestCase
{
    public function testIsViolationSkipped(): void
    {
        $configuration = [
            'ClassWithOneDep' => [
                'DependencyClass',
            ],
            'ClassWithEmptyDeps' => [],
            'ClassWithMultipleDeps' => [
                'DependencyClass1',
                'DependencyClass2',
                'DependencyClass2',
            ],
        ];
        $helper = new SkippedViolationHelper($configuration);

        self::assertTrue(
            $helper->isViolationSkipped(
                ClassLikeToken::fromFQCN('ClassWithOneDep')->toString(),
                ClassLikeToken::fromFQCN('DependencyClass')->toString()
            )
        );
        self::assertFalse(
            $helper->isViolationSkipped(
                ClassLikeToken::fromFQCN('ClassWithEmptyDeps')->toString(),
                ClassLikeToken::fromFQCN('DependencyClass')->toString()
            )
        );
        self::assertTrue(
            $helper->isViolationSkipped(
                ClassLikeToken::fromFQCN('ClassWithMultipleDeps')->toString(),
                ClassLikeToken::fromFQCN('DependencyClass1')->toString()
            )
        );
        self::assertTrue(
            $helper->isViolationSkipped(
                ClassLikeToken::fromFQCN('ClassWithMultipleDeps')->toString(),
                ClassLikeToken::fromFQCN('DependencyClass2')->toString()
            )
        );
    }

    public function testUnmatchedSkippedViolations(): void
    {
        $configuration = [
            'ClassWithOneDep' => [
                'DependencyClass',
            ],
            'ClassWithEmptyDeps' => [],
            'ClassWithMultipleDeps' => [
                'DependencyClass1',
                'DependencyClass2',
                'DependencyClass2',
            ],
        ];
        $helper = new SkippedViolationHelper($configuration);

        self::assertTrue(
            $helper->isViolationSkipped(
                ClassLikeToken::fromFQCN('ClassWithOneDep')->toString(),
                ClassLikeToken::fromFQCN('DependencyClass')->toString()
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
