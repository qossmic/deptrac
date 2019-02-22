<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\DependencyEmitter;

use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\Dependency\Result;

interface DependencyEmitterInterface
{
    public function getName(): string;

    public function applyDependencies(AstMap $astMap, Result $dependencyResult): void;
}
