<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency\Emitter;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\Contracts\Dependency\EmitterInterface;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\Result;

class FileDependencyEmitter implements EmitterInterface
{
    public function applyDependencies(AstMap $astMap, Result $dependencyResult): void
    {
        foreach ($astMap->getAstFileReferences() as $fileReference) {
            $dependencies = $fileReference->getDependencies();
            foreach ($dependencies as $emittedDependency) {
                if (AstMap\AstDependency::USE === $emittedDependency->getType()) {
                    continue;
                }
                $dependencyResult->addDependency(
                    new Dependency(
                        $fileReference->getTokenName(),
                        $emittedDependency->getTokenName(),
                        $emittedDependency->getFileOccurrence()
                    )
                );
            }
        }
    }
}
