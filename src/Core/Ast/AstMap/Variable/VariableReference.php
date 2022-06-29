<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\Variable;

use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenReferenceInterface;

/**
 * @psalm-immutable
 */
class VariableReference implements TokenReferenceInterface
{
    private SuperGlobalToken $tokenName;

    public function __construct(SuperGlobalToken $tokenName)
    {
        $this->tokenName = $tokenName;
    }

    public function getFileReference(): ?FileReference
    {
        return null;
    }

    public function getToken(): TokenInterface
    {
        return $this->tokenName;
    }
}
