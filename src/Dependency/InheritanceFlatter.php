<?php

namespace SensioLabs\Deptrac\Dependency;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstMap\FlattenAstInherit;
use SensioLabs\Deptrac\DependencyResult\InheritDependency;

class InheritanceFlatter
{
    public function flattenDependencies(
        AstMap $astMap,
        Result $dependencyResult
    ): void {
        foreach ($astMap->getAstClassReferences() as $classReference) {
            foreach ($astMap->getClassInherits($classReference->getClassName()) as $inherit) {
                if (!$inherit instanceof FlattenAstInherit) {
                    continue;
                }

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
