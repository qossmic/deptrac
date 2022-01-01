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
                        !$this->isNamespace($emittedDependency, $astMap)
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

    protected function isNamespace(AstDependency $dependency, AstMap $astMap): bool
    {
        $dependencyFQDN = $dependency->getTokenName()->toString();

        $functionReferences = array_merge(...array_values(array_map(
            static function ($file) { return $file->getFunctionReferences(); },
            $astMap->getAstFileReferences()
        )));
        $references = array_merge($astMap->getAstClassReferences(), $functionReferences);

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

        return $isNamespace && !$isFQDN;
    }
}
