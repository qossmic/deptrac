<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency\Emitter;

use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Ast\AstMap\FunctionLike\FunctionLikeToken;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\DependencyList;

final class FunctionDependencyEmitter implements DependencyEmitterInterface
{
    public static function getAlias(): string
    {
        return EmitterTypes::FUNCTION_TOKEN;
    }

    public function getName(): string
    {
        return 'FunctionDependencyEmitter';
    }

    public function applyDependencies(AstMap $astMap, DependencyList $dependencyList): void
    {
        foreach ($astMap->getFileReferences() as $astFileReference) {
            foreach ($astFileReference->getFunctionLikeReferences() as $astFunctionReference) {
                foreach ($astFunctionReference->getDependencies() as $dependency) {
                    if (DependencyToken::SUPERGLOBAL_VARIABLE === $dependency->getType()) {
                        continue;
                    }

                    if (DependencyToken::UNRESOLVED_FUNCTION_CALL === $dependency->getType()) {
                        $token = $dependency->getToken();
                        assert($token instanceof FunctionLikeToken);
                        if (null === $astMap->getFunctionReferenceForToken($token)) {
                            continue;
                        }
                    }

                    $dependencyList->addDependency(
                        new Dependency(
                            $astFunctionReference->getToken(),
                            $dependency->getToken(),
                            $dependency->getFileOccurrence()
                        )
                    );
                }
            }
        }
    }
}
