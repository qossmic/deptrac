<?php


namespace DependencyTracker\Tests;


use DependencyTracker\ClassNameLayerResolver;
use DependencyTracker\Collector\CollectorInterface;
use DependencyTracker\CollectorFactory;
use DependencyTracker\Configuration;
use DependencyTracker\Configuration\ConfigurationCollector;
use DependencyTracker\Configuration\ConfigurationLayer;
use DependencyTracker\Configuration\ConfigurationRuleset;
use DependencyTracker\DependencyResult;
use DependencyTracker\DependencyResult\Dependency;
use DependencyTracker\RulesetEngine;
use Prophecy\Argument;
use SensioLabs\AstRunner\AstMap;

class RulesetEngineTest extends \PHPUnit_Framework_TestCase
{

    public function testGetViolationsButNoViolations()
    {
        $engine = new RulesetEngine();

        $dependencyResult = (new DependencyResult())
            ->addDependency(new Dependency('A', 23, 'B'));

        $classNameLayerResolver = $this->getClassNameLayerResolver();

        $configurationRuleset = ConfigurationRuleset::fromArray([
            'LayerA' => [
                'LayerB',
                'LayerC'
            ],
            'LayerD' => []
        ]);

        $this->assertCount(
            0,
            $engine->getViolations($dependencyResult, $classNameLayerResolver, $configurationRuleset)
        );
    }

    public function testGetViolationsWithUnknownLayer()
    {
        $engine = new RulesetEngine();

        $dependencyResult = (new DependencyResult())
            ->addDependency($dependency = new Dependency('A', 23, 'B'));

        $classNameLayerResolver = $this->getClassNameLayerResolver();

        $configurationRuleset = ConfigurationRuleset::fromArray([]);

        $violations = $engine->getViolations($dependencyResult, $classNameLayerResolver, $configurationRuleset);

        $this->assertCount(1, $violations);
        $this->assertEquals('LayerA', $violations[0]->getLayerA());
        $this->assertEquals('LayerB', $violations[0]->getLayerB());
        $this->assertSame($dependency, $violations[0]->getDependency());
    }

    public function testGetViolationLayer()
    {
        $engine = new RulesetEngine();

        $dependencyResult = (new DependencyResult())
            ->addDependency($dependency = new Dependency('A', 23, 'B'));

        $classNameLayerResolver = $this->getClassNameLayerResolver();

        $configurationRuleset = ConfigurationRuleset::fromArray([
            'LayerA' => ['LayerB']
        ]);

        $violations = $engine->getViolations($dependencyResult, $classNameLayerResolver, $configurationRuleset);

        $this->assertCount(0, $violations);
    }

    public function testGetViolationsWithLayerDecl()
    {
        $engine = new RulesetEngine();

        $dependencyResult = (new DependencyResult())
            ->addDependency($dependency = new Dependency('A', 23, 'B'));

        $classNameLayerResolver = $this->getClassNameLayerResolver();

        $configurationRuleset = ConfigurationRuleset::fromArray([
            'LayerA' => ['LayerD']
        ]);

        $violations = $engine->getViolations($dependencyResult, $classNameLayerResolver, $configurationRuleset);

        $this->assertCount(1, $violations);
        $this->assertEquals('LayerA', $violations[0]->getLayerA());
        $this->assertEquals('LayerB', $violations[0]->getLayerB());
        $this->assertSame($dependency, $violations[0]->getDependency());
    }

    /**
     * @return ClassNameLayerResolver
     */
    private function getClassNameLayerResolver()
    {
        $configurationCollector = $this->prophesize(ConfigurationCollector::class);
        $configurationCollector->getType()->willReturn('');
        $configurationCollector->getArgs()->willReturn([]);

        $configurationLayer = $this->prophesize(ConfigurationLayer::class);
        $configurationLayer->getCollectors()->willReturn([$configurationCollector->reveal()]);

        $configuration = $this->prophesize(Configuration::class);
        $configuration->getLayers()->willReturn([$configurationLayer->reveal()]);

        $collectorFactory = $this->prophesize(CollectorFactory::class);
        $collectorFactory->getCollector(Argument::any())->willReturn($this->prophesize(CollectorInterface::class)->reveal());

        return new ClassNameLayerResolver(
            $configuration->reveal(),
            $this->prophesize(AstMap::class)->reveal(),
            $collectorFactory->reveal()
        );
    }

}
