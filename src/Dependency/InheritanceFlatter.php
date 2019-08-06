<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Dependency;

use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\DependencyResult\InheritDependency;

class InheritanceFlatter
{
    public function flattenDependencies(
        AstMap $astMap,
        Result $dependencyResult
    ): void {
        foreach ($astMap->getAstClassReferences() as $classReference) {
            foreach ($astMap->getClassInherits($classReference->getClassName()) as $inherit) {
                foreach ($dependencyResult->getDependenciesByClass($inherit->getClassName()) as $dep) {
                    $dependencyResult->addInheritDependency(new InheritDependency(
                        $classReference->getClassName(),
                        $dep->getClassB(),
                        $dep,
                        $inherit
                    ));
                }
            }
        }
    }
}
