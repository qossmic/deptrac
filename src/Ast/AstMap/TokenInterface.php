<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\AstMap;

/**
 * Represents an AST-Token, which can be referenced as dependency.
 */
interface TokenInterface
{
    public function toString(): string;
}
