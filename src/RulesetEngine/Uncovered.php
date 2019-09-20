<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\RulesetEngine;

use SensioLabs\Deptrac\Dependency\DependencyInterface;

final class Uncovered implements Rule
{
    private $dependency;
    private $layer;

    public function __construct(DependencyInterface $dependency, string $layer)
    {
        $this->dependency = $dependency;
        $this->layer = $layer;
    }

    public function getDependency(): DependencyInterface
    {
        return $this->dependency;
    }

    public function getLayer(): string
    {
        return $this->layer;
    }
}
