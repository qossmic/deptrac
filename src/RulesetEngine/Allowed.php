<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\RulesetEngine;

use SensioLabs\Deptrac\Dependency\DependencyInterface;

final class Allowed implements Rule
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
