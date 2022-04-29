<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency\Emitter;

use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Dependency\DependencyList;

interface DependencyEmitterInterface
{
    public static function getAlias(): string;

    public function getName(): string;

    public function applyDependencies(AstMap $astMap, DependencyList $dependencyList): void;
}
