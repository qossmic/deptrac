<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\DependencyEmitter;

use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\Dependency\Dependency;

class InheritanceDependencyEmitter implements DependencyEmitterInterface
{
    public function getName(): string
    {
        return 'InheritanceDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, Result $dependencyResult): void
    {
        foreach ($astMap->getAstClassReferences() as $classReference) {
            foreach ($astMap->getClassInherits($classReference->getClassName()) as $inherit) {
                $dependencyResult->addDependency(
                    new Dependency(
                        $classReference->getFileReference()->getFilepath(),
                        $classReference->getClassName(),
                        $inherit->getLine(),
                        $inherit->getClassName()
                    )
                );
            }
        }
    }
}
