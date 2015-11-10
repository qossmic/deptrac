<?php 

namespace DependencyTracker\DependencyEmitter;

use DependencyTracker\AstMap;
use DependencyTracker\DependencyResult;

class FlattenDependencyEmitter implements DependencyEmitterInterface
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'FlattenDependencyEmitter';
    }

    /**
     * @param AstMap $astMap
     * @param DependencyResult $dependencyResult
     * @return void
     */
    public function applyDependencies(AstMap $astMap, DependencyResult $dependencyResult)
    {
        foreach ($astMap->getAllFlattenClassInherits() as $class => $inherits) {
            /** @var AstMap\FlattenAstInherit[] $inherits */
            foreach ($inherits as $inherit) {

                foreach ($inherit->all() as $foo) {

                    foreach ($dependencyResult->getDependenciesByClass($foo) as $dep) {

                        $dependencyResult->addDependency(
                        /** @var AstMap\FlattenAstInherit $inherit */
                            new DependencyResult\InheritDependency(
                                $class,
                                0,
                                $dep->getClassB(),
                                $dep->,
                                array_map(function($classes){

                                }, $inherit->all())
                            )
                        );

                    }
                }
            }
        }
    }
}
