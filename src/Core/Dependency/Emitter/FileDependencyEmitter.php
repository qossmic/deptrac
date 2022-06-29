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
            $dependencies = $fileReference->getDependencies();
            foreach ($dependencies as $dependency) {
                if (DependencyToken::USE === $dependency->getType()) {
                    continue;
                }

                if (DependencyToken::UNRESOLVED_FUNCTION_CALL === $dependency->getType()) {
                    continue;
                }

                $dependencyList->addDependency(
                    new Dependency(
                        $fileReference->getToken(),
                        $dependency->getToken(),
                        $dependency->getFileOccurrence()
                    )
                );
            }
        }
    }
}
