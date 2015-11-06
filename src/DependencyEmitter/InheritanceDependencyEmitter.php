<?php

namespace DependencyTracker\DependencyEmitter;

use DependencyTracker\AstMap;
use DependencyTracker\DependencyResult;
use DependencyTracker\DependencyResult\Dependency;

class InheritanceDependencyEmitter implements DependencyEmitterInterface
{
    public function getName()
    {
        return 'InheritanceDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, DependencyResult $dependencyResult)
    {
        foreach ($astMap->getAllInherits() as $class => $inherits) {
            foreach ($inherits as $inherit) {
                $dependencyResult->addDependency(
                    new DependencyResult\Dependency(
                        $class, '?', $inherit, '?', '?'
                    )
                );
            }
        }

        foreach ($astMap->getAllFlattenClassInherits() as $class => $inherits) {
            foreach ($inherits as $inherit) {
                $dependencyResult->addDependency(
                    new Dependency(
                        $class, '?', $inherit, '?', '?'
                    )
                );
            }
        }
    }

}
