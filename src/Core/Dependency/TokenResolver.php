<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Dependency;

use Qossmic\Deptrac\Contract\Ast\TokenInterface;
use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileToken;
use Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike\FunctionLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike\FunctionLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\SuperGlobalToken;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\VariableReference;
use Qossmic\Deptrac\Supportive\ShouldNotHappenException;

class TokenResolver
{
    public function resolve(TokenInterface $token, AstMap $astMap): TokenReferenceInterface
    {
        return match (true) {
            $token instanceof ClassLikeToken => $astMap->getClassReferenceForToken($token) ?? new ClassLikeReference($token),
            $token instanceof FunctionLikeToken => $astMap->getFunctionReferenceForToken($token) ?? new FunctionLikeReference($token),
            $token instanceof SuperGlobalToken => new VariableReference($token),
            $token instanceof FileToken => $astMap->getFileReferenceForToken($token) ?? new FileReference($token->path, [], [], []),
            default => throw new ShouldNotHappenException()
        };
    }
}
