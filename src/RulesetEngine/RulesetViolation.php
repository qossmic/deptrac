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

    public function __construct(DependencyInterface $dependency, string $layerA, string $layerB)
    {
        $this->dependency = $dependency;
        $this->layerA = $layerA;
        $this->layerB = $layerB;
    }

    /**
     * @return Dependency|InheritDependency
     */
    public function getDependency(): DependencyInterface
    {
        return $this->dependency;
    }

    public function getLayerA(): string
    {
        return $this->layerA;
    }

    public function getLayerB(): string
    {
        return $this->layerB;
    }
}
