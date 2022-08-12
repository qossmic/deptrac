<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\Variable;

use Qossmic\Deptrac\Core\Ast\AstMap\TokenInterface;

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

    public function __construct(private readonly string $name)
    {
    }

    public function toString(): string
    {
        return '$'.$this->name;
    }
}
