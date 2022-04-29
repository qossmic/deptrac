<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Ast\AstMap\File\FileToken;
use Qossmic\Deptrac\Ast\AstMap\FunctionLike\FunctionLikeReference;
use Qossmic\Deptrac\Ast\AstMap\FunctionLike\FunctionLikeToken;
use Qossmic\Deptrac\Ast\AstMap\TokenInterface;
use Qossmic\Deptrac\Ast\AstMap\TokenReferenceInterface;
use Qossmic\Deptrac\Ast\AstMap\Variable\SuperGlobalToken;
use Qossmic\Deptrac\Ast\AstMap\Variable\VariableReference;
use Qossmic\Deptrac\Exception\ShouldNotHappenException;

class TokenResolver
{
    public function resolve(TokenInterface $token, AstMap $astMap): TokenReferenceInterface
    {
        if ($token instanceof ClassLikeToken) {
            return $astMap->getClassReferenceForToken($token) ?? new ClassLikeReference($token);
        }

        if ($token instanceof FunctionLikeToken) {
            return $astMap->getFunctionReferenceForToken($token) ?? new FunctionLikeReference($token);
        }

        if ($token instanceof SuperGlobalToken) {
            return new VariableReference($token);
        }

        if ($token instanceof FileToken) {
            return $astMap->getFileReferenceForToken($token) ?? new FileReference($token->getFilepath(), [], [], []);
        }

        throw new ShouldNotHappenException();
    }
}
