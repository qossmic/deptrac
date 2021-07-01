<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\AstRunner\AstMap;

class InheritanceFlatter
{
    public function flattenDependencies(
        AstMap $astMap,
        Result $dependencyResult
    ): void {
        foreach ($astMap->getAstClassReferences() as $classReference) {
            $tokenName = $classReference->getTokenName();
            foreach ($astMap->getClassInherits($tokenName) as $inherit) {
                foreach ($dependencyResult->getDependenciesByClass($inherit->getClassLikeName()) as $dep) {
                    $depTokenName = $dep->getDependee();
                    assert($depTokenName instanceof AstMap\ClassLikeName);
                    $dependencyResult->addInheritDependency(new InheritDependency(
                                                                $tokenName,
                                                                $depTokenName,
                                                                $dep,
                                                                $inherit
                    ));
                }
            }
        }
    }
}
