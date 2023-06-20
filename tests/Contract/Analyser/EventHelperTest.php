<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Contract\Analyser;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Analyser\EventHelper;
use Qossmic\Deptrac\Contract\Layer\LayerProvider;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;

final class EventHelperTest extends TestCase
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
        $helper = new EventHelper($configuration, new LayerProvider([]));

        self::assertTrue(
            $helper->shouldViolationBeSkipped(
                ClassLikeToken::fromFQCN('ClassWithOneDep')->toString(),
                ClassLikeToken::fromFQCN('DependencyClass')->toString()
            )
        );

        // also skipps multiple occurrences
        self::assertTrue(
            $helper->shouldViolationBeSkipped(
                ClassLikeToken::fromFQCN('ClassWithOneDep')->toString(),
                ClassLikeToken::fromFQCN('DependencyClass')->toString()
            )
        );
        self::assertFalse(
            $helper->shouldViolationBeSkipped(
                ClassLikeToken::fromFQCN('ClassWithEmptyDeps')->toString(),
                ClassLikeToken::fromFQCN('DependencyClass')->toString()
            )
        );
        self::assertTrue(
            $helper->shouldViolationBeSkipped(
                ClassLikeToken::fromFQCN('ClassWithMultipleDeps')->toString(),
                ClassLikeToken::fromFQCN('DependencyClass1')->toString()
            )
        );
        self::assertTrue(
            $helper->shouldViolationBeSkipped(
                ClassLikeToken::fromFQCN('ClassWithMultipleDeps')->toString(),
                ClassLikeToken::fromFQCN('DependencyClass2')->toString()
            )
        );
        self::assertFalse(
            $helper->shouldViolationBeSkipped(
                ClassLikeToken::fromFQCN('DependencyClass')->toString(),
                ClassLikeToken::fromFQCN('ClassWithOneDep')->toString()
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
        $helper = new EventHelper($configuration, new LayerProvider([]));

        self::assertTrue(
            $helper->shouldViolationBeSkipped(
                ClassLikeToken::fromFQCN('ClassWithOneDep')->toString(),
                ClassLikeToken::fromFQCN('DependencyClass')->toString()
            )
        );
        // also skipps multiple occurrences
        self::assertTrue(
            $helper->shouldViolationBeSkipped(
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
