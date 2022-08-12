<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\ClassLike;

use Qossmic\Deptrac\Core\Ast\AstMap\TokenInterface;

final class ClassLikeType implements TokenInterface
{
    private const TYPE_CLASSLIKE = 'classLike';
    private const TYPE_CLASS = 'class';
    private const TYPE_INTERFACE = 'interface';
    private const TYPE_TRAIT = 'trait';

    private function __construct(private readonly string $type)
    {
    }

    public static function classLike(): self
    {
        return new self(self::TYPE_CLASSLIKE);
    }

    public static function class(): self
    {
        return new self(self::TYPE_CLASS);
    }

    public static function interface(): self
    {
        return new self(self::TYPE_INTERFACE);
    }

    public static function trait(): self
    {
        return new self(self::TYPE_TRAIT);
    }

    public function matches(ClassLikeType $type): bool
    {
        return $this->toString() === $type->toString();
    }

    public function toString(): string
    {
        return $this->type;
    }

    //TODO: Replace with String representation (Patrick Kusebauch @ 12.08.22)
    public function __toString()
    {
        return $this->type;
    }
}
