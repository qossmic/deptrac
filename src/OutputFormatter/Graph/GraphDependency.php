<?php

namespace SensioLabs\Deptrac\OutputFormatter\Graph;

use SensioLabs\Deptrac\Configuration\ConfigurationLayer;
use SensioLabs\Deptrac\DependencyResult\DependencyInterface;
use SensioLabs\Deptrac\LayerResolver\ResolvedLayer;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;

class GraphDependency
{
    /** @var ResolvedLayer */
    private $layerA;

    /** @var ResolvedLayer */
    private $layerB;

    /** @var DependencyInterface[] */
    private $dependencies = [];

    /** @var RulesetViolation[] */
    private $violations = [];

    /**
     * GraphDependency constructor.
     * @param ResolvedLayer $layerA
     * @param ResolvedLayer $layerB
     */
    public function __construct(ResolvedLayer $layerA, ResolvedLayer $layerB)
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

    /** @return ResolvedLayer */
    public function getLayerA()
    {
        return $this->layerA;
    }

    /** @return ResolvedLayer */
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
