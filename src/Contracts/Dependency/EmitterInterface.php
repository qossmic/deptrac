<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contracts\Dependency;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\Dependency\Result;

interface EmitterInterface
{
    public function applyDependencies(AstMap $astMap, Result $dependencyResult): void;
}
