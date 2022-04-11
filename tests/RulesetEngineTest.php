<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\AstRunner\AstMap\TokenName;
use Qossmic\Deptrac\Configuration\Configuration;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\Result;
use Qossmic\Deptrac\RulesetEngine;
use Qossmic\Deptrac\TokenLayerResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @covers \Qossmic\Deptrac\RulesetEngine
 */
final class RulesetEngineTest extends TestCase
{
    private function createDependencies(array $fromTo): iterable
    {
        foreach ($fromTo as $from => $to) {
            yield new Dependency(
                ClassLikeName::fromFQCN($from),
                ClassLikeName::fromFQCN($to),
                FileOccurrence::fromFilepath('foo.php', 0)
            );
        }
    }

    public function dependencyProvider(): iterable
    {
        yield [
            [
                // ClassA has a Dependency on ClassB
                'ClassA' => 'ClassB',
            ],
            [
                // ClassA is in LayerA, ClassB is in LayerB
                'ClassA' => ['LayerA'],
                'ClassB' => ['LayerB'],
            ],
            [
                [
                    'name' => 'LayerA',
                    'collectors' => [],
                ],
                [
                    'name' => 'LayerB',
                    'collectors' => [],
                ],
            ],
            [
                'LayerA' => [
                    'LayerB',
                ],
            ],
            0,
        ];

        yield [
            [
                'ClassA' => 'ClassB',
            ],
            [
                'ClassA' => ['LayerA'],
                'ClassB' => ['LayerB'],
            ],
            [
                [
                    'name' => 'LayerA',
                    'collectors' => [],
                ],
                [
                    'name' => 'LayerB',
                    'collectors' => [],
                ],
            ],
            [
                'LayerA' => [],
                'LayerB' => [],
            ],
            1,
        ];

        yield [
            [
                'ClassA' => 'ClassB',
            ],
            [
                'ClassA' => ['LayerA'],
                'ClassB' => ['LayerB'],
            ],
            [
                [
                    'name' => 'LayerA',
                    'collectors' => [],
                ],
                [
                    'name' => 'LayerB',
                    'collectors' => [],
                ],
            ],
            [],
            1,
        ];

        yield [
            [
                'ClassA' => 'ClassB',
            ],
            [
                'ClassA' => [],
                'ClassB' => [],
            ],
            [],
            [],
            0,
        ];

        yield [
            [
                'ClassA' => 'ClassB',
            ],
            [
                'ClassA' => ['LayerA'],
                'ClassB' => ['LayerB'],
            ],
            [
                [
                    'name' => 'LayerA',
                    'collectors' => [],
                ],
                [
                    'name' => 'LayerB',
                    'collectors' => [],
                ],
            ],
            [
                'LayerA' => ['LayerB'],
            ],
            0,
        ];

        yield [
            [
                'ClassA' => 'ClassB',
            ],
            [
                'ClassA' => ['LayerA'],
                'ClassB' => ['LayerB'],
            ],
            [
                [
                    'name' => 'LayerA',
                    'collectors' => [],
                ],
                [
                    'name' => 'LayerB',
                    'collectors' => [],
                ],
            ],
            [
                'LayerB' => ['LayerA'],
            ],
            1,
        ];

        yield [
            [
                'ClassA' => 'ClassB',
                'ClassB' => 'ClassA',
                'ClassC' => 'ClassD',
            ],
            [
                'ClassA' => ['LayerA'],
                'ClassB' => ['LayerB'],
                'ClassC' => ['LayerC'],
                'ClassD' => ['LayerD'],
            ],
            [
                [
                    'name' => 'LayerA',
                    'collectors' => [],
                ],
                [
                    'name' => 'LayerB',
                    'collectors' => [],
                ],
                [
                    'name' => 'LayerC',
                    'collectors' => [],
                ],
                [
                    'name' => 'LayerD',
                    'collectors' => [],
                ],
            ],
            [],
            3,
        ];

        yield [
            [
                'ClassA' => 'ClassA',
            ],
            [
                'ClassA' => ['LayerA'],
            ],
            [
                [
                    'name' => 'LayerA',
                    'collectors' => [],
                ],
            ],
            [],
            0,
        ];
    }

    /**
     * @dataProvider dependencyProvider
     */
    public function testProcess(
        array $dependenciesAsArray,
        array $classesInLayers,
        array $layersConfiguration,
        array $rulesetConfiguration,
        int $expectedCount
    ): void {
        $dependencyResult = new Result();
        foreach ($this->createDependencies($dependenciesAsArray) as $dep) {
            $dependencyResult->addDependency($dep);
        }

        $tokenLayerResolver = $this->createMock(TokenLayerResolverInterface::class);
        $tokenLayerResolver->method('getLayersByTokenName')->willReturnCallback(
            static function (TokenName $tokenName) use ($classesInLayers) {
                return $classesInLayers[$tokenName->toString()];
            }
        );

        $configuration = Configuration::fromArray([
            'layers' => $layersConfiguration,
            'paths' => [],
            'ruleset' => $rulesetConfiguration,
        ]);

        $context = (new RulesetEngine(new EventDispatcher()))->process(
            $dependencyResult,
            $tokenLayerResolver,
            $configuration->getRuleset()
        );

        self::assertCount($expectedCount, $context->violations());
    }

    public function provideTestGetSkippedViolations(): array
    {
        return [
            'not skipped violations' => [
                [
                    'ClassA' => 'ClassB',
                    'ClassB' => 'ClassA',
                ],
                [
                    'ClassA' => ['LayerA'],
                    'ClassB' => ['LayerB'],
                ],
                [],
                0,
            ],
            'has skipped violations' => [
                [
                    'ClassA' => 'ClassB',
                    'ClassB' => 'ClassA',
                ],
                [
                    'ClassA' => ['LayerA'],
                    'ClassB' => ['LayerB'],
                ],
                [
                    'ClassA' => [
                        'ClassB',
                    ],
                    'ClassB' => [
                        'ClassA',
                    ],
                ],
                2,
            ],
        ];
    }

    /**
     * @dataProvider provideTestGetSkippedViolations
     */
    public function testGetSkippedViolations(array $dependenciesAsArray, array $classesInLayers, array $skippedViolationsConfig, int $expectedSkippedViolationCount): void
    {
        $dependencyResult = new Result();
        foreach ($this->createDependencies($dependenciesAsArray) as $dep) {
            $dependencyResult->addDependency($dep);
        }

        $tokenLayerResolver = $this->createMock(TokenLayerResolverInterface::class);
        $tokenLayerResolver->method('getLayersByTokenName')->willReturnCallback(
            static function (TokenName $tokenName) use ($classesInLayers) {
                return $classesInLayers[$tokenName->toString()];
            }
        );

        $configuration = Configuration::fromArray([
            'layers' => [],
            'paths' => [],
            'ruleset' => [],
            'skip_violations' => $skippedViolationsConfig,
        ]);

        $context = (new RulesetEngine(new EventDispatcher()))->process(
            $dependencyResult,
            $tokenLayerResolver,
            $configuration->getRuleset()
        );

        self::assertCount($expectedSkippedViolationCount, $context->skippedViolations());
    }

    public function provideTestIgnoreUncoveredInternalClasses(): iterable
    {
        yield [
            ['ClassA' => 'RuntimeException'],
            [
                'ClassA' => ['LayerA'],
                'RuntimeException' => [],
            ],
            'ignoreUncoveredInternalClasses' => true,
            0,
        ];

        yield [
            ['ClassA' => 'RuntimeException'],
            [
                'ClassA' => ['LayerA'],
                'RuntimeException' => [],
            ],
            'ignoreUncoveredInternalClasses' => false,
            1,
        ];
    }

    /**
     * @dataProvider provideTestIgnoreUncoveredInternalClasses
     */
    public function testIgnoreUncoveredInternalClasses(array $dependenciesAsArray, array $classesInLayers, bool $ignoreUncoveredInternalClasses, int $expectedUncoveredCount): void
    {
        $dependencyResult = new Result();
        foreach ($this->createDependencies($dependenciesAsArray) as $dep) {
            $dependencyResult->addDependency($dep);
        }

        $tokenLayerResolver = $this->createMock(TokenLayerResolverInterface::class);
        $tokenLayerResolver->method('getLayersByTokenName')->willReturnCallback(
            static function (TokenName $tokenName) use ($classesInLayers) {
                return $classesInLayers[$tokenName->toString()];
            }
        );

        $configuration = Configuration::fromArray([
            'layers' => [],
            'paths' => [],
            'ruleset' => [],
            'skip_violations' => [],
            'ignore_uncovered_internal_classes' => $ignoreUncoveredInternalClasses,
        ]);

        $context = (new RulesetEngine(new EventDispatcher()))->process(
            $dependencyResult,
            $tokenLayerResolver,
            $configuration->getRuleset()
        );

        self::assertCount($expectedUncoveredCount, $context->uncovered());
    }
}
