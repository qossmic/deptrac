<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\RulesetEngine;

use SensioLabs\Deptrac\Dependency\DependencyInterface;

interface Rule
{
    public function getDependency(): DependencyInterface;
}
