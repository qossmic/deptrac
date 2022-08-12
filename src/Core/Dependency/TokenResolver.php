<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Dependency;

use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileToken;
use Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike\FunctionLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike\FunctionLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenReferenceInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\SuperGlobalToken;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\VariableReference;
use Qossmic\Deptrac\Supportive\ShouldNotHappenException;

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
            return $astMap->getFileReferenceForToken($token) ?? new FileReference($token->path, [], [], []);
        }

        throw new ShouldNotHappenException();
    }
}
