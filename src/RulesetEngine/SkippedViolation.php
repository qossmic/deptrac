<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\RulesetEngine;

use Qossmic\Deptrac\Dependency\DependencyInterface;

/**
 * @psalm-immutable
 */
final class SkippedViolation implements CoveredRule
{
    private DependencyInterface $dependency;
    private string $dependantLayerName;
    private string $dependeeLayerName;

    public function __construct(DependencyInterface $dependency, string $dependantLayerName, string $dependeeLayerName)
    {
        $this->dependency = $dependency;
        $this->dependantLayerName = $dependantLayerName;
        $this->dependeeLayerName = $dependeeLayerName;
    }

    public function getDependency(): DependencyInterface
    {
        return $this->dependency;
    }

    public function getDependantLayerName(): string
    {
        return $this->dependantLayerName;
    }

    public function getDependeeLayerName(): string
    {
        return $this->dependeeLayerName;
    }
}
