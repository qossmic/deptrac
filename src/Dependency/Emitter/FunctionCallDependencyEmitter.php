<?php

declare(strict_types = 1);

namespace Qossmic\Deptrac\Dependency\Emitter;

use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Ast\AstMap\FunctionLike\FunctionLikeToken;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\DependencyList;

final class FunctionCallDependencyEmitter implements DependencyEmitterInterface
{
    public static function getAlias(): string
    {
        return EmitterTypes::FUNCTION_CALL;
    }

    public function getName(): string
    {
        return 'FunctionCallDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, DependencyList $dependencyList): void
    {
        foreach ($astMap->getClassLikeReferences() as $classReference) {
            $classLikeName = $classReference->getToken();

            foreach ($classReference->getDependencies() as $dependency) {
                if (DependencyToken::UNRESOLVED_FUNCTION_CALL !== $dependency->getType()) {
                    continue;
                }

                $token = $dependency->getToken();
                assert($token instanceof FunctionLikeToken);
                if (null === $astMap->getFunctionReferenceForToken($token)) {
                    continue;
                }

                $dependencyList->addDependency(
                    new Dependency(
                        $classLikeName, $dependency->getToken(), $dependency->getFileOccurrence()
                    )
                );
            }
        }
    }
}
