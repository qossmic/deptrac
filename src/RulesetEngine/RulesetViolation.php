<?php

namespace SensioLabs\Deptrac\RulesetEngine;

use SensioLabs\Deptrac\Configuration\ConfigurationLayer;
use SensioLabs\Deptrac\DependencyResult\Dependency;
use SensioLabs\Deptrac\DependencyResult\DependencyInterface;
use SensioLabs\Deptrac\DependencyResult\InheritDependency;
use SensioLabs\Deptrac\LayerResolver\ResolvedLayer;

class RulesetViolation
{
    /** @var DependencyInterface */
    private $dependeny;

    /** @var ResolvedLayer */
    private $layerA;

    /** @var ResolvedLayer */
    private $layerB;

    /**
     * @param DependencyInterface $dependeny
     * @param ResolvedLayer $layerA
     * @param ResolvedLayer $layerB
     */
    public function __construct(DependencyInterface $dependeny, ResolvedLayer $layerA, ResolvedLayer $layerB)
    {
        $this->dependeny = $dependeny;
        $this->layerA = $layerA;
        $this->layerB = $layerB;
    }


    /** @return Dependency|InheritDependency */
    public function getDependency()
    {
        return $this->dependeny;
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
}
