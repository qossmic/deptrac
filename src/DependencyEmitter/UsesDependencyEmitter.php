<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\DependencyEmitter;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstDependency;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\Result;

class UsesDependencyEmitter implements DependencyEmitterInterface
{
    public function getName(): string
    {
        return 'UsesDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, Result $dependencyResult): void
    {
        foreach ($astMap->getAstFileReferences() as $fileReference) {
            $dependencies = $fileReference->getDependencies();
            foreach ($fileReference->getAstClassReferences() as $astClassReference) {
                foreach ($dependencies as $emittedDependency) {
                    if (AstDependency::USE === $emittedDependency->getType() &&
                        $this->isFQDN($emittedDependency, $astMap)
                    ) {
                        $dependencyResult->addDependency(
                            new Dependency(
                                $astClassReference->getTokenName(),
                                $emittedDependency->getTokenName(),
                                $emittedDependency->getFileOccurrence()
                            )
                        );
                    }
                }
            }
        }
    }

    protected function isFQDN(AstDependency $dependency, AstMap $astMap): bool
    {
        $dependencyFQDN = $dependency->getTokenName()->toString();
        $references = array_merge($astMap->getAstClassReferences(), $astMap->getAstFunctionReferences());

        $isFQDN = false;
        $isNamespace = false;

        foreach ($references as $reference) {
            $referenceFQDN = $reference->getTokenName()->toString();

            if ($referenceFQDN === $dependencyFQDN) {
                $isFQDN = true;
            }

            if (0 === strpos($referenceFQDN, $dependencyFQDN, 0) &&
                $referenceFQDN !== $dependencyFQDN
            ) {
                $isNamespace = true;
            }
        }

        return !$isNamespace || $isFQDN;
    }
}
