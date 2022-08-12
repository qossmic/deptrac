<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Result;

use Qossmic\Deptrac\Core\Dependency\DependencyInterface;

/**
 * @psalm-immutable
 */
final class Uncovered implements RuleInterface
{
    public function __construct(private readonly DependencyInterface $dependency, private readonly string $layer)
    {
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
