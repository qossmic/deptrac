<?php


namespace DependencyTracker\Tests;


use DependencyTracker\Configuration\ConfigurationRuleset;
use DependencyTracker\DependencyResult;
use DependencyTracker\DependencyResult\Dependency;
use DependencyTracker\RulesetEngine;

class RulesetEngineTest extends \PHPUnit_Framework_TestCase
{

    public function testGetViolationsButNoViolations()
    {
        $engine = new RulesetEngine();

        $dependencyResult = (new DependencyResult())
            ->addDependency(new Dependency('A', 23, 'B'))
            ->addClassToLayer('A', 'LayerA')
            ->addClassToLayer('B', 'LayerB')
        ;

        $configurationRuleset = ConfigurationRuleset::fromArray([
            'LayerA' => [
                'LayerB',
                'LayerC'
            ],
            'LayerD' => []
        ]);

        $this->assertCount(
            0,
            $engine->getViolations($dependencyResult, $configurationRuleset)
        );
    }

    public function testGetViolationsWithUnknownLayer()
    {
        $engine = new RulesetEngine();

        $dependencyResult = (new DependencyResult())
            ->addDependency($dependency = new Dependency('A', 23, 'B'))
            ->addClassToLayer('A', 'LayerA')
            ->addClassToLayer('B', 'LayerB')
        ;

        $configurationRuleset = ConfigurationRuleset::fromArray([]);

        $violations = $engine->getViolations($dependencyResult, $configurationRuleset);

        $this->assertCount(1, $violations);
        $this->assertEquals('LayerA', $violations[0]->getLayerA());
        $this->assertEquals('LayerB', $violations[0]->getLayerB());
        $this->assertSame($dependency, $violations[0]->getDependency());
    }

    public function testGetViolationLayer()
    {
        $engine = new RulesetEngine();

        $dependencyResult = (new DependencyResult())
            ->addDependency($dependency = new Dependency('A', 23, 'B'))
            ->addClassToLayer('A', 'LayerA')
            ->addClassToLayer('B', 'LayerB')
        ;

        $configurationRuleset = ConfigurationRuleset::fromArray([
            'LayerA' => ['LayerB']
        ]);

        $violations = $engine->getViolations($dependencyResult, $configurationRuleset);

        $this->assertCount(0, $violations);
    }

    public function testGetViolationsWithLayerDecl()
    {
        $engine = new RulesetEngine();

        $dependencyResult = (new DependencyResult())
            ->addDependency($dependency = new Dependency('A', 23, 'B'))
            ->addClassToLayer('A', 'LayerA')
            ->addClassToLayer('B', 'LayerB')
        ;

        $configurationRuleset = ConfigurationRuleset::fromArray([
            'LayerA' => ['LayerD']
        ]);

        $violations = $engine->getViolations($dependencyResult, $configurationRuleset);

        $this->assertCount(1, $violations);
        $this->assertEquals('LayerA', $violations[0]->getLayerA());
        $this->assertEquals('LayerB', $violations[0]->getLayerB());
        $this->assertSame($dependency, $violations[0]->getDependency());
    }

}
