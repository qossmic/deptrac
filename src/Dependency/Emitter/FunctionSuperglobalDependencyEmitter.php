<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency\Emitter;

use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\DependencyList;

final class FunctionSuperglobalDependencyEmitter implements DependencyEmitterInterface
{
    public function getName(): string
    {
        return 'FunctionSuperglobalDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, DependencyList $dependencyList): void
    {
        foreach ($astMap->getFileReferences() as $astFileReference) {
            foreach ($astFileReference->getFunctionLikeReferences() as $astFunctionReference) {
                foreach ($astFunctionReference->getDependencies() as $dependency) {
                    if (DependencyToken::SUPERGLOBAL_VARIABLE !== $dependency->getType()) {
                        continue;
                    }
                    $dependencyList->addDependency(
                        new Dependency(
                            $astFunctionReference->getToken(),
                            $dependency->getToken(),
                            $dependency->getFileOccurrence()
                        )
                    );
                }
            }
        }
    }
}
