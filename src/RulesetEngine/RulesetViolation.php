<?php 

namespace DependencyTracker\RulesetEngine;

use DependencyTracker\DependencyResult\Dependency;
use DependencyTracker\DependencyResult\InheritDependency;

class RulesetViolation
{
    private $dependeny;

    private $codeSnippet;

    private $layerA;

    private $layerB;

    /**
     * @param Dependency $dependency
     * @param $layerA
     * @param $layerB
     * @param null $codeSnippet
     */
    public function __construct(Dependency $dependency, $layerA, $layerB, $codeSnippet = null)
    {
        $this->dependeny = $dependency;
        $this->codeSnippet = $codeSnippet;
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
    public function getCodeSnippet()
    {
        return $this->codeSnippet;
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
