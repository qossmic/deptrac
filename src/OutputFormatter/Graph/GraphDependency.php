<?php

namespace SensioLabs\Deptrac\OutputFormatter\Graph;

use SensioLabs\Deptrac\Configuration\ConfigurationLayer;
use SensioLabs\Deptrac\DependencyResult\DependencyInterface;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;

class GraphDependency
{
    /** @var ConfigurationLayer */
    private $layerA;

    /** @var ConfigurationLayer */
    private $layerB;

    /** @var DependencyInterface[] */
    private $dependencies = [];

    /** @var RulesetViolation[] */
    private $violations = [];

    /**
     * GraphDependency constructor.
     * @param ConfigurationLayer $layerA
     * @param ConfigurationLayer $layerB
     */
    public function __construct(ConfigurationLayer $layerA, ConfigurationLayer $layerB)
    {
        $this->layerA = $layerA;
        $this->layerB = $layerB;
    }

    /** @param DependencyInterface $dependency */
    public function addDependency(DependencyInterface $dependency)
    {
        $this->dependencies[] = $dependency;
    }

    public function addViolation(RulesetViolation $rulesetViolation)
    {
        $this->violations[] = $rulesetViolation;
    }

    /** @return ConfigurationLayer */
    public function getLayerA()
    {
        return $this->layerA;
    }

    /** @return ConfigurationLayer */
    public function getLayerB()
    {
        return $this->layerB;
    }

    /** @return DependencyInterface[] */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /** @return RulesetViolation[] */
    public function getViolations()
    {
        return $this->violations;
    }


}
