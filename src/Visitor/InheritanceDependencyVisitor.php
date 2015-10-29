<?php 

namespace DependencyTracker\Visitor;

use DependencyTracker\AstHelper;
use DependencyTracker\AstMap;
use DependencyTracker\DependencyResult;
use DependencyTracker\DependencyResult\InheritDependency;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;

class InheritanceDependencyVisitor
{
    public function flattenInheritanceDependencies(AstMap $astMap, DependencyResult $dependencyResult)
    {

        $dependencies = [];

        # build a hasMap with inheritDependencie
        # [CLASS] = [INHERIT_FROM, INHERIT_FROM]

        /** @var $klass ClassLike */
        foreach (AstHelper::findClassLikeNodes($astMap->getAsts()) as $klass) {

            if (!$klass instanceof Class_ || !$klass->namespacedName instanceof Name) {
                continue;
            }

            if (!isset($dependencies[$klass->namespacedName->toString()])) {
                $dependencies[$klass->namespacedName->toString()] = [];
            }

            if ($klass->extends instanceof Name) {
                $dependencies[$klass->namespacedName->toString()][] = $klass->extends->toString();
            }

            if (!empty($klass->implements)) {
                foreach ($klass->implements as $impl) {

                    if (!$impl instanceof Name) {
                        continue;
                    }

                    $dependencies[$klass->namespacedName->toString()][] = $impl->toString();
                }
            }
        }

        $flattenDependencies = [];
        foreach ($dependencies as $klass => $deps) {

            $flattenDependencies[$klass] = [];

            foreach ($deps as $dependency) {
                $flattenDependencies[$klass] = array_values(array_unique(array_merge(
                    $flattenDependencies[$klass],
                    isset($dependencies[$dependency]) ? $dependencies[$dependency] : []
                )));
            }

            if (empty($flattenDependencies[$klass])) {
                unset($flattenDependencies[$klass]);
            }
        }

        foreach ($flattenDependencies as $klass => $deps) {
            foreach ($deps as $dependency) {
                foreach ($dependencyResult->getDependenciesByClass($dependency) as $dependencyOfDependency) {
                    if ($klass == $dependencyOfDependency->getClassA()) {
                        continue;
                    }

                    $dependencyResult->addInheritDependency(InheritDependency::fromDependency($klass, 0, $dependencyOfDependency));
                }
            }
        }

    }
}
