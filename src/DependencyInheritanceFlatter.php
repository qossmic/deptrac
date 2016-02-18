<?php

namespace DependencyTracker;

use DependencyTracker\DependencyResult\InheritDependency;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstMap\FlattenAstInherit;

class DependencyInheritanceFlatter
{
    public function flattenDependencies(
        AstMap $astMap,
        DependencyResult $dependencyResult
    ) {
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
