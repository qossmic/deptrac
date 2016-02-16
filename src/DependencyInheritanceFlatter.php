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

            $class = $classReference->getClassName();

            $dependenciesToInherit = [];

            foreach ($astMap->getClassInherits($class) as $inherit) {

                if (!$inherit instanceof FlattenAstInherit) {
                    continue;
                }

                $dependenciesForClass = $dependencyResult->getDependenciesByClass($inherit->getClassName());


                foreach ($dependenciesForClass as $dep) {

                    $dependencyResult->addInheritDependency(new InheritDependency(
                        $class,
                        $dep->getClassB(),
                        $dep,
                        $inherit
                    ));
                }

            }
        }

    }
}
