<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Result;

use Qossmic\Deptrac\Dependency\DependencyInterface;

/**
 * @psalm-immutable
 */
interface Rule
{
    public function getDependency(): DependencyInterface;
}
