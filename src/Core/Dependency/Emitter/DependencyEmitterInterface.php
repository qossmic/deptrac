<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Dependency\Emitter;

use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Dependency\DependencyList;
interface DependencyEmitterInterface
{
    public function getName() : string;
    public function applyDependencies(AstMap $astMap, DependencyList $dependencyList) : void;
}
