<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Dependency\Emitter;

use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Dependency\Dependency;
use Qossmic\Deptrac\Core\Dependency\DependencyList;

final class FunctionSuperglobalDependencyEmitter implements DependencyEmitterInterface
{
    public function getName(): string
    {
        return 'FunctionSuperglobalDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, DependencyList $dependencyList): void
    {
        foreach ($astMap->getFileReferences() as $astFileReference) {
            foreach ($astFileReference->functionReferences as $astFunctionReference) {
                foreach ($astFunctionReference->dependencies as $dependency) {
                    if (DependencyType::SUPERGLOBAL_VARIABLE !== $dependency->context->dependencyType) {
                        continue;
                    }
                    $dependencyList->addDependency(
                        new Dependency(
                            $astFunctionReference->getToken(),
                            $dependency->token,
                            $dependency->context,
                        )
                    );
                }
            }
        }
    }
}
