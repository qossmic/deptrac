<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency\Emitter;

use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\DependencyList;

final class ClassSuperglobalDependencyEmitter implements DependencyEmitterInterface
{

    public function getName(): string
    {
        return 'ClassSuperglobalDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, DependencyList $dependencyList): void
    {
        foreach ($astMap->getClassLikeReferences() as $classReference) {
            foreach ($classReference->getDependencies() as $dependency) {
                if (DependencyToken::SUPERGLOBAL_VARIABLE !== $dependency->getType()) {
                    continue;
                }
                $dependencyList->addDependency(
                    new Dependency(
                        $classReference->getToken(),
                        $dependency->getToken(),
                        $dependency->getFileOccurrence()
                    )
                );
            }
        }
    }
}
