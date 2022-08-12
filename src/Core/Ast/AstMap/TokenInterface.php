<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap;

use Stringable;

/**
 * Represents an AST-Token, which can be referenced as dependency.
 */
interface TokenInterface extends Stringable
{
    public function toString(): string;
}
