<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

interface AstInheritInterface
{
    public const TYPE_EXTENDS = 1;
    public const TYPE_IMPLEMENTS = 2;
    public const TYPE_USES = 3;

    public function __toString(): string;

    public function getClassName(): string;

    public function getLine(): int;

    public function getType(): int;

    /** @return AstInheritInterface[] */
    public function getPath(): array;
}
