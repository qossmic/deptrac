<?php

namespace SensioLabs\Deptrac\Tests;

use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\Configuration\ConfigurationRuleset;
use SensioLabs\Deptrac\DependencyResult;
use SensioLabs\Deptrac\DependencyResult\Dependency;
use SensioLabs\Deptrac\RulesetEngine;

class RulesetEngineTest extends \PHPUnit_Framework_TestCase
{
    private function createDependencies(array $fromTo)
    {
        foreach ($fromTo as $from => $to) {
            if (is_array($to)) {
                yield new Dependency($from, 0, $to[0], $to[1]);
            } else {
                yield new Dependency($from, 0, $to);
            }
        }
    }

    public function dependencyProvider()
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

        yield [
            [
                'ClassA' => ['ClassB', 'new'],
            ],
            [
                'ClassA' => ['LayerA'],
                'ClassB' => ['LayerB'],
            ],
            [
                'LayerA' => [
                    'LayerB' => ['include' => ['use']]
                ],
            ],
            1,
        ];

        yield [
            [
                'ClassA' => ['ClassB', 'new'],
            ],
            [
                'ClassA' => ['LayerA'],
                'ClassB' => ['LayerB'],
            ],
            [
                'LayerA' => [
                    'LayerB' => ['exclude' => ['new']]
                ],
            ],
            1,
        ];

        yield [
            [
                'ClassA' => ['ClassB', 'extends'],
            ],
            [
                'ClassA' => ['LayerA'],
                'ClassB' => ['LayerB'],
            ],
            [
                'LayerA' => [
                    'LayerB' => null
                ],
            ],
            0,
        ];

        yield [
            [
                'ClassA' => ['ClassB', 'use'],
            ],
            [
                'ClassA' => ['LayerA'],
                'ClassB' => ['LayerB'],
                'ClassC' => ['LayerC'],
            ],
            [
                'LayerA' => [
                    'LayerC' => null
                ],
            ],
            1,
        ];
    }

    /**
     * @param array $dependenciesAsArray
     * @param array $classesInLayers
     * @param array $rulesetConfiguration
     * @param $expectedCount
     * @dataProvider dependencyProvider
     */
    public function testGetViolationsButNoViolations(array $dependenciesAsArray, array $classesInLayers, array $rulesetConfiguration, $expectedCount)
    {
        $dependencyResult = (new DependencyResult());
        foreach ($this->createDependencies($dependenciesAsArray) as $dep) {
            $dependencyResult->addDependency($dep);
        }

        $classNameLayerResolver = $this->prophesize(ClassNameLayerResolverInterface::class);
        foreach ($classesInLayers as $classInLayer => $layers) {
            $classNameLayerResolver->getLayersByClassName($classInLayer)->willReturn($layers);
        }

        $this->assertCount(
            $expectedCount,
            (new RulesetEngine())->getViolations(
                $dependencyResult,
                $classNameLayerResolver->reveal(),
                ConfigurationRuleset::fromArray($rulesetConfiguration)
            )
        );
    }
}
