<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency\Emitter;

use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\DependencyList;

final class FileDependencyEmitter implements DependencyEmitterInterface
{
    public static function getAlias(): string
    {
        return EmitterTypes::FILE_TOKEN;
    }

    public function getName(): string
    {
        return 'FileDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, DependencyList $dependencyList): void
    {
        foreach ($astMap->getFileReferences() as $fileReference) {
            $dependencies = $fileReference->getDependencies();
            foreach ($dependencies as $emittedDependency) {
                if (DependencyToken::USE === $emittedDependency->getType()) {
                    continue;
                }
                $dependencyList->addDependency(
                    new Dependency(
                        $fileReference->getToken(),
                        $emittedDependency->getToken(),
                        $emittedDependency->getFileOccurrence()
                    )
                );
            }
        }
    }
}
