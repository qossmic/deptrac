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
    public function __construct(private readonly SuperGlobalToken $tokenName)
    {
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
