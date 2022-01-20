<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\DependencyEmitter;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstDependency;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\Result;
use function array_map;
use function array_merge;
use function explode;

class UsesDependencyEmitter implements DependencyEmitterInterface
{
    public function getName(): string
    {
        return 'UsesDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, Result $dependencyResult): void
    {
        $references = array_merge($astMap->getAstClassReferences(), $astMap->getAstFunctionReferences());
        $referencesFQDN = array_map(
            static function ($ref) {
                return $ref->getTokenName()->toString();
            },
            $references
        );

        $FQDNIndex = new FQDNIndexNode();
        foreach ($referencesFQDN as $reference) {
            $path = explode('\\', $reference);
            $FQDNIndex->setNestedNode($path);
        }

        foreach ($astMap->getAstFileReferences() as $fileReference) {
            $dependencies = $fileReference->getDependencies();
            foreach ($fileReference->getAstClassReferences() as $astClassReference) {
                foreach ($dependencies as $emittedDependency) {
                    if (AstDependency::USE === $emittedDependency->getType() &&
                        $this->isFQDN($emittedDependency, $FQDNIndex)
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

    protected function isFQDN(AstDependency $dependency, FQDNIndexNode $FQDNIndex): bool
    {
        $dependencyFQDN = $dependency->getTokenName()->toString();
        $path = explode('\\', $dependencyFQDN);
        $value = $FQDNIndex->getNestedNode($path);
        if (null === $value) {
            return true;
        }

        return $value->isFQDN();
    }
}
