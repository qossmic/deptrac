<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Result;

use Qossmic\Deptrac\Core\Dependency\DependencyInterface;

/**
 * @psalm-immutable
 */
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
