<?php

namespace SensioLabs\Deptrac\RulesetEngine;

use SensioLabs\Deptrac\DependencyResult\Dependency;
use SensioLabs\Deptrac\DependencyResult\DependencyInterface;
use SensioLabs\Deptrac\DependencyResult\InheritDependency;

class RulesetViolation
{
    private $dependency;

    private $layerA;

    private $layerB;

    /**
     * @param DependencyInterface $dependency
     * @param $layerA
     * @param $layerB
     */
    public function __construct(DependencyInterface $dependency, $layerA, $layerB)
    {
        $this->dependency = $dependency;
        $this->layerA = $layerA;
        $this->layerB = $layerB;
    }

    /**
     * @return Dependency|InheritDependency
     */
    public function getDependency()
    {
        return $this->dependency;
    }

    /**
     * @return mixed
     */
    public function getLayerA()
    {
        return $this->layerA;
    }

    /**
     * @return mixed
     */
    public function getLayerB()
    {
        return $this->layerB;
    }
}
