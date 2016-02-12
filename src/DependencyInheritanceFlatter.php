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

            foreach ($dependencyResult->getDependenciesByClass($classReference->getClassName()) as $dep) {

                foreach ($astMap->getClassInherits($dep->getClassA()) as $inherit) {

                    // for now we just care about direct inheritance
                    if (!$inherit instanceof FlattenAstInherit) {
                        continue;
                    }

                    $dependencyResult->addInheritDependency(
                        new InheritDependency(
                            $classReference->getClassName(),
                            $inherit
                        )
                    );

                }
            }
        }
    }
}
