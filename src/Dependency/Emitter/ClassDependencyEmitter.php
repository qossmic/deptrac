<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency\Emitter;

use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\DependencyList;

final class ClassDependencyEmitter implements DependencyEmitterInterface
{

    public function getName(): string
    {
        return 'ClassDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, DependencyList $dependencyList): void
    {
        foreach ($astMap->getClassLikeReferences() as $classReference) {
            $classLikeName = $classReference->getToken();

            foreach ($classReference->getDependencies() as $dependency) {
                if (DependencyToken::SUPERGLOBAL_VARIABLE === $dependency->getType()) {
                    continue;
                }
                if (DependencyToken::UNRESOLVED_FUNCTION_CALL === $dependency->getType()) {
                    continue;
                }

                $dependencyList->addDependency(
                    new Dependency(
                        $classLikeName,
                        $dependency->getToken(),
                        $dependency->getFileOccurrence()
                    )
                );
            }

            foreach ($astMap->getClassInherits($classLikeName) as $inherit) {
                $dependencyList->addDependency(
                    new Dependency(
                        $classLikeName,
                        $inherit->getClassLikeName(),
                        $inherit->getFileOccurrence()
                    )
                );
            }
        }
    }
}
