<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap;

//TODO: Add Superglobal Token (Patrick Kusebauch @ 10.07.21)
/**
 * @psalm-immutable
 */
interface TokenName
{
    public function toString(): string;
}
