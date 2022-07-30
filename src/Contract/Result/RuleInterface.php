<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Result;

use Qossmic\Deptrac\Core\Dependency\DependencyInterface;

/**
 * @psalm-immutable
 */
interface RuleInterface
{
    public function getDependency(): DependencyInterface;
}
