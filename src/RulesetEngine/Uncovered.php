<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\RulesetEngine;

use Qossmic\Deptrac\Dependency\DependencyInterface;

final class Uncovered implements Rule
{
    private DependencyInterface $dependency;
    private string $layer;

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
