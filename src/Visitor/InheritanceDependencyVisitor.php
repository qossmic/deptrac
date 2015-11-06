<?php

namespace DependencyTracker\Visitor;

use DependencyTracker\AstMap;
use DependencyTracker\DependencyResult;
use DependencyTracker\DependencyResult\InheritDependency;
use PhpParser\Node\Name;

class InheritanceDependencyVisitor
{
    public function flattenInheritanceDependencies(AstMap $astMap, DependencyResult $dependencyResult)
    {
        foreach ($astMap->getAllFlattenClassInherits() as $class => $inherits) {
            foreach ($inherits as $inherit) {

                // inheritance is a dependency, too
                $dependencyResult->addInheritDependency(
                    new InheritDependency($class, 0, $inherit, null, 0)
                );

                continue;
                foreach ($dependencyResult->getDependenciesByClass($inherit) as $dependencyOfDependency) {
                    $dependencyResult->addInheritDependency(
                        InheritDependency::fromDependency($class, 0, $dependencyOfDependency)
                    );
                }
            }
        }
    }
}
