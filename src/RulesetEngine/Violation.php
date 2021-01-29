<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\RulesetEngine;

use Qossmic\Deptrac\Dependency\DependencyInterface;

final class Violation implements Rule
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
