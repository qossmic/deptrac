<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\DependencyEmitter;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\Result;

class ClassDependencyEmitter implements DependencyEmitterInterface
{
    public function getName(): string
    {
        return 'ClassDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, Result $dependencyResult): void
    {
        foreach ($astMap->getAstClassReferences() as $classReference) {
            $classLikeName = $classReference->getTokenName();

            foreach ($classReference->getDependencies() as $dependency) {
                $dependencyResult->addDependency(
                    new Dependency(
                        $classLikeName,
                        $dependency->getTokenName(),
                        $dependency->getFileOccurrence()
                    )
                );
            }

            foreach ($astMap->getClassInherits($classLikeName) as $inherit) {
                $dependencyResult->addDependency(
                    new Dependency(
                        $classLikeName,
                        $inherit->getClassLikeName(),
                        $inherit->getFileOccurrence()
                    )
                );
            }
        }
    }
}
