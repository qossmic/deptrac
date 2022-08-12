<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Dependency\Emitter;

use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Dependency\Dependency;
use Qossmic\Deptrac\Core\Dependency\DependencyList;

final class FileDependencyEmitter implements DependencyEmitterInterface
{
    public function getName(): string
    {
        return 'FileDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, DependencyList $dependencyList): void
    {
        foreach ($astMap->getFileReferences() as $fileReference) {
            foreach ($fileReference->dependencies as $dependency) {
                if (DependencyToken::USE === $dependency->type) {
                    continue;
                }

                if (DependencyToken::UNRESOLVED_FUNCTION_CALL === $dependency->type) {
                    continue;
                }

                $dependencyList->addDependency(
                    new Dependency(
                        $fileReference->getToken(),
                        $dependency->token,
                        $dependency->fileOccurrence
                    )
                );
            }
        }
    }
}
