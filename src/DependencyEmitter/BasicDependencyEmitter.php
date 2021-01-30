<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\DependencyEmitter;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstDependency;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\Result;

class BasicDependencyEmitter implements DependencyEmitterInterface
{
    public function getName(): string
    {
        return 'BasicDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, Result $dependencyResult): void
    {
        foreach ($astMap->getAstFileReferences() as $fileReference) {
            $uses = $fileReference->getDependencies();

            foreach ($fileReference->getAstClassReferences() as $astClassReference) {
                /** @var AstDependency[] $dependencies */
                $dependencies = array_merge($uses, $astClassReference->getDependencies());

                foreach ($dependencies as $emittedDependency) {
                    $dependencyResult->addDependency(
                        new Dependency(
                            $astClassReference->getClassLikeName(),
                            $emittedDependency->getClassLikeName(),
                            $emittedDependency->getFileOccurrence()
                        )
                    );
                }
            }
        }
    }
}
