<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\Configuration\ConfigurationRuleset;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyResult\Dependency;
use SensioLabs\Deptrac\RulesetEngine;

class RulesetEngineTest extends TestCase
{
    private function createDependencies(array $fromTo): iterable
    {
        foreach ($fromTo as $from => $to) {
            yield new Dependency($from, 0, $to);
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
                'LayerC' => [],
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
    public function testGetViolationsButNoViolations(array $dependenciesAsArray, array $classesInLayers, array $rulesetConfiguration, int $expectedCount): void
    {
        $dependencyResult = (new Result());
        foreach ($this->createDependencies($dependenciesAsArray) as $dep) {
            $dependencyResult->addDependency($dep);
        }

        $classNameLayerResolver = $this->prophesize(ClassNameLayerResolverInterface::class);
        foreach ($classesInLayers as $classInLayer => $layers) {
            $classNameLayerResolver->getLayersByClassName($classInLayer)->willReturn($layers);
        }

        static::assertCount(
            $expectedCount,
            (new RulesetEngine())->getViolations(
                $dependencyResult,
                $classNameLayerResolver->reveal(),
                ConfigurationRuleset::fromArray($rulesetConfiguration)
            )
        );
    }
}
