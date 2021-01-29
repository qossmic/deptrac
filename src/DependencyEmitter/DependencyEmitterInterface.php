<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\DependencyEmitter;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\Dependency\Result;

interface DependencyEmitterInterface
{
    public function getName(): string;

    public function applyDependencies(AstMap $astMap, Result $dependencyResult): void;
}
