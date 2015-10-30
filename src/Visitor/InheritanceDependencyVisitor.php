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

        foreach ($dependencyResult->getDependencies() as $dependency) {
            if (!isset($dependencies[$dependency->getClassA()])) {
                $dependencies[$dependency->getClassA()] = [];
            }

            $dependencies[$dependency->getClassA()] = array_values(array_unique(array_merge($dependencies[$dependency->getClassA()], [$dependency->getClassB()])));
        }

        $flattenDependencies = [];
        foreach ($dependencies as $klass => $deps) {
            $flattenDependencies[$klass] = $this->resolveDepsRecursive($klass, $dependencies);
        }

        foreach ($flattenDependencies as $klass => $deps) {
            $buffer = [];
            foreach ($deps as $dependency) {

                foreach ($dependencyResult->getDependenciesByClass($dependency) as $dependencyOfDependency) {
                    if ($klass == $dependencyOfDependency->getClassA()) {
                        continue;
                    }

                    // filter direct dependencies
                    if (in_array($x = $dependencyOfDependency->getClassB(), $y = $dependencies[$klass])) {
                        continue;
                    }

                    $buffer[spl_object_hash($dependencyOfDependency)] = InheritDependency::fromDependency($klass, 0, $dependencyOfDependency);
                }
            }

            foreach ($buffer as $v) {
                $dependencyResult->addInheritDependency($v);
            }
        }


    }

    private function resolveDepsRecursive($class, array &$deps)
    {
        $buffer = [];
        foreach ($deps[$class] as $dep) {

            if (isset($deps[$dep])) {
                $buffer = array_merge($buffer, $this->resolveDepsRecursive($dep, $deps));
            }

            $buffer[] = $dep;

        }

        return array_values(array_unique($buffer));
    }
}
