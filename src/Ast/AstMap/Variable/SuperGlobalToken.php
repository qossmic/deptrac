<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\AstMap\Variable;

use Qossmic\Deptrac\Ast\AstMap\TokenInterface;

final class SuperGlobalToken implements TokenInterface
{
    public const ALLOWED_NAMES = [
        'GLOBALS',
        '_SERVER',
        '_GET',
        '_POST',
        '_FILES',
        '_COOKIE',
        '_SESSION',
        '_REQUEST',
        '_ENV',
    ];

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
