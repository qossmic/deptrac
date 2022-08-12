<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Result;

use Qossmic\Deptrac\Contract\Dependency\DependencyInterface;

/**
 * @psalm-immutable
 */
final class Uncovered implements RuleInterface
{
    public function __construct(
        private readonly DependencyInterface $dependency,
        public readonly string $layer
    ) {
    }

    public function getDependency(): DependencyInterface
    {
        return $this->dependency;
    }
}
