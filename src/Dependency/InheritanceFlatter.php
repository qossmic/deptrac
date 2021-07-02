<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\AstRunner\AstMap;

class InheritanceFlatter
{
    public function flattenDependencies(
        AstMap $astMap,
        Result $dependencyResult
    ): void {
        foreach ($astMap->getAstClassReferences() as $classReference) {
            $classLikeName = $classReference->getTokenName();
            foreach ($astMap->getClassInherits($classLikeName) as $inherit) {
                foreach ($dependencyResult->getDependenciesByClass($inherit->getClassLikeName()) as $dep) {
                    $dependencyResult->addInheritDependency(
                        new InheritDependency(
                            $classLikeName, $dep->getDependee(), $dep, $inherit
                        )
                    );
                }
            }
        }
    }
}
