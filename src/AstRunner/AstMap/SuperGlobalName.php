<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap;

final class SuperGlobalName implements TokenName
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function toString(): string
    {
        return '$'.$this->name;
    }
}
