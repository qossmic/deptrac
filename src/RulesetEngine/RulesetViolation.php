<?php 

namespace DependencyTracker\RulesetEngine;

use DependencyTracker\DependencyResult\Dependency;
use DependencyTracker\DependencyResult\InheritDependency;

class RulesetViolation
{
    private $dependeny;

    private $layerA;

    private $layerB;

    /**
     * @param Dependency $dependency
     * @param $layerA
     * @param $layerB
     */
    public function __construct(Dependency $dependency, $layerA, $layerB)
    {
        $this->dependeny = $dependency;
        $this->layerA = $layerA;
        $this->layerB = $layerB;
    }

    /**
     * @return Dependency|InheritDependency
     */
    public function getDependeny()
    {
        return $this->dependeny;
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
