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
            $tokenLikeName = $classReference->getTokenLikeName();
            assert($tokenLikeName instanceof AstMap\ClassLikeName);
            foreach ($astMap->getClassInherits($tokenLikeName) as $inherit) {
                foreach ($dependencyResult->getDependenciesByClass($inherit->getClassLikeName()) as $dep) {
                    $depTokenName = $dep->getTokenLikeNameB();
                    assert($depTokenName instanceof AstMap\ClassLikeName);
                    $dependencyResult->addInheritDependency(new InheritDependency(
                                                                $tokenLikeName,
                                                                $depTokenName,
                                                                $dep,
                                                                $inherit
                    ));
                }
            }
        }
    }
}
