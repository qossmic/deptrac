<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\Configuration\Configuration;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\RulesetEngine;

class RulesetEngineTest extends TestCase
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
            [],
            0,
        ];
    }

    /**
     * @dataProvider dependencyProvider
     */
    public function testProcess(array $dependenciesAsArray, array $classesInLayers, array $rulesetConfiguration, int $expectedCount): void
    {
        $dependencyResult = new Result();
        foreach ($this->createDependencies($dependenciesAsArray) as $dep) {
            $dependencyResult->addDependency($dep);
        }

        $classNameLayerResolver = $this->prophesize(ClassNameLayerResolverInterface::class);
        foreach ($classesInLayers as $classInLayer => $layers) {
            $classNameLayerResolver->getLayersByClassName(ClassLikeName::fromFQCN($classInLayer))->willReturn($layers);
        }

        $configuration = Configuration::fromArray([
            'layers' => [],
            'paths' => [],
            'ruleset' => $rulesetConfiguration,
        ]);

        $context = (new RulesetEngine())->process(
            $dependencyResult,
            $classNameLayerResolver->reveal(),
            $configuration
        );

        static::assertCount($expectedCount, $context->violations());
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

        $classNameLayerResolver = $this->prophesize(ClassNameLayerResolverInterface::class);
        foreach ($classesInLayers as $classInLayer => $layers) {
            $classNameLayerResolver->getLayersByClassName(ClassLikeName::fromFQCN($classInLayer))->willReturn($layers);
        }

        $configuration = Configuration::fromArray([
            'layers' => [],
            'paths' => [],
            'ruleset' => [],
            'skip_violations' => $skippedViolationsConfig,
        ]);

        $context = (new RulesetEngine())->process(
            $dependencyResult,
            $classNameLayerResolver->reveal(),
            $configuration
        );

        static::assertCount($expectedSkippedViolationCount, $context->skippedViolations());
    }

    public function provideTestGetUncovered(): array
    {
        return [
            'uncovered' => [
                [
                    'ClassA' => 'ClassB',
                ],
                [
                    'ClassA' => ['LayerA'],
                    'ClassB' => [],
                ],
                1,
            ],
            'internal classes are not counted towards uncovered cases' => [
                [
                    'ClassA' => 'DateTime',
                ],
                [
                    'ClassA' => ['LayerA'],
                    'DateTime' => [],
                ],
                0,
            ],
        ];
    }

    /**
     * @dataProvider provideTestGetUncovered
     */
    public function testGetUncovered(array $dependenciesAsArray, array $classesInLayers, int $expectedUncoveredCount): void
    {
        $dependencyResult = new Result();
        foreach ($this->createDependencies($dependenciesAsArray) as $dep) {
            $dependencyResult->addDependency($dep);
        }

        $classNameLayerResolver = $this->prophesize(ClassNameLayerResolverInterface::class);
        foreach ($classesInLayers as $classInLayer => $layers) {
            $classNameLayerResolver->getLayersByClassName(ClassLikeName::fromFQCN($classInLayer))->willReturn($layers);
        }

        $configuration = Configuration::fromArray([
            'layers' => [],
            'paths' => [],
            'ruleset' => [],
        ]);

        $context = (new RulesetEngine())->process(
            $dependencyResult,
            $classNameLayerResolver->reveal(),
            $configuration
        );

        static::assertCount($expectedUncoveredCount, $context->uncovered());
    }
}
