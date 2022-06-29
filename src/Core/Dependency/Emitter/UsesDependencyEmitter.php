<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Dependency\Emitter;

use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Dependency\Dependency;
use Qossmic\Deptrac\Core\Dependency\DependencyList;

use function array_map;
use function array_merge;
use function explode;

final class UsesDependencyEmitter implements DependencyEmitterInterface
{
    public function getName(): string
    {
        return 'UsesDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, DependencyList $dependencyList): void
    {
        $references = array_merge($astMap->getClassLikeReferences(), $astMap->getFunctionLikeReferences());
        $referencesFQDN = array_map(
            static function ($ref) {
                return $ref->getToken()->toString();
            },
            $references
        );

        $FQDNIndex = new FQDNIndexNode();
        foreach ($referencesFQDN as $reference) {
            $path = explode('\\', $reference);
            $FQDNIndex->setNestedNode($path);
        }

        foreach ($astMap->getFileReferences() as $fileReference) {
            $dependencies = $fileReference->getDependencies();
            foreach ($fileReference->getClassLikeReferences() as $astClassReference) {
                foreach ($dependencies as $emittedDependency) {
                    if (DependencyToken::USE === $emittedDependency->getType() &&
                        $this->isFQDN($emittedDependency, $FQDNIndex)
                    ) {
                        $dependencyList->addDependency(
                            new Dependency(
                                $astClassReference->getToken(),
                                $emittedDependency->getToken(),
                                $emittedDependency->getFileOccurrence()
                            )
                        );
                    }
                }
            }
        }
    }

    private function isFQDN(DependencyToken $dependency, FQDNIndexNode $FQDNIndex): bool
    {
        $dependencyFQDN = $dependency->getToken()->toString();
        $path = explode('\\', $dependencyFQDN);
        $value = $FQDNIndex->getNestedNode($path);
        if (null === $value) {
            return true;
        }

        return $value->isFQDN();
    }
}
