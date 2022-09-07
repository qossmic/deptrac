<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Dependency\Emitter;

use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyTokenType;
use Qossmic\Deptrac\Core\Dependency\Dependency;
use Qossmic\Deptrac\Core\Dependency\DependencyList;

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

            foreach ($classReference->dependencies as $dependency) {
                if (DependencyTokenType::SUPERGLOBAL_VARIABLE === $dependency->type) {
                    continue;
                }
                if (DependencyTokenType::UNRESOLVED_FUNCTION_CALL === $dependency->type) {
                    continue;
                }

                $dependencyList->addDependency(
                    new Dependency(
                        $classLikeName,
                        $dependency->token,
                        $dependency->fileOccurrence
                    )
                );
            }

            foreach ($astMap->getClassInherits($classLikeName) as $inherit) {
                $dependencyList->addDependency(
                    new Dependency(
                        $classLikeName,
                        $inherit->classLikeName,
                        $inherit->fileOccurrence
                    )
                );
            }
        }
    }
}
