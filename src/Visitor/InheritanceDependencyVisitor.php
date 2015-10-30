<?php 

namespace DependencyTracker\Visitor;

use DependencyTracker\AstHelper;
use DependencyTracker\AstMap;
use DependencyTracker\DependencyResult;
use DependencyTracker\DependencyResult\InheritDependency;
use DependencyTracker\Tests\Visitor\Fixtures\MultipleInteritanceC;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;

class InheritanceDependencyVisitor
{
    public function flattenInheritanceDependencies(AstMap $astMap, DependencyResult $dependencyResult)
    {

        $dependencies = [];

        foreach ($astMap->getAsts() as $ast) {
            foreach (AstHelper::findClassLikeNodes($ast) as $classLike) {
                foreach (AstHelper::findInheritances($classLike) as $classLikesInheritance) {

                    if (!$classLike->namespacedName instanceof Name) {
                        continue;
                    }

                    if (!isset($dependencies[$classLike->namespacedName->toString()])) {
                        $dependencies[$classLike->namespacedName->toString()] = [];
                    }

                    $dependencies[$classLike->namespacedName->toString()][] = $classLikesInheritance;
                }
            }
        }

        $flattenDependencies = [];
        foreach ($dependencies as $klass => $deps) {
            $flattenDependencies[$klass] = $this->resolveDepsRecursive($klass, $dependencies);
        }

        foreach ($flattenDependencies as $klass => $deps) {
            foreach ($deps as $dependency) {
                foreach ($dependencyResult->getDependenciesByClass($dependency) as $dependencyOfDependency) {
                    if ($klass == $dependencyOfDependency->getClassA()) {
                        continue;
                    }

                    // filter direct dependencies
                    if (in_array($x = $dependencyOfDependency->getClassB(), $y = $dependencies[$klass])) {
                        continue;
                    }

                    $dependencyResult->addInheritDependency(InheritDependency::fromDependency($klass, 0, $dependencyOfDependency));
                }
            }
        }
    }

    private function resolveDepsRecursive($class, array &$deps, \ArrayObject $alreadyResolved = null)
    {
        if ($alreadyResolved == null) {
            $alreadyResolved = new \ArrayObject();
        }

        // recursion detected
        if (isset($alreadyResolved[$class])) {
            return [];
        }

        $alreadyResolved[$class] = true;

        $buffer = [];
        foreach ($deps[$class] as $dep) {

            if (isset($deps[$dep])) {
                $buffer = array_merge($buffer, $this->resolveDepsRecursive($dep, $deps, $alreadyResolved));
            }

            $buffer[] = $dep;
        }

        return array_values(array_unique($buffer));
    }
}
