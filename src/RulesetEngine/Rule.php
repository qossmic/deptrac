<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\RulesetEngine;

use Qossmic\Deptrac\Dependency\DependencyInterface;

/**
 * @psalm-immutable
 */
interface Rule
{
    public function getDependency(): DependencyInterface;
}
