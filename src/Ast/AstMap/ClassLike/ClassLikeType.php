<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\AstMap\ClassLike;

use LogicException;
use Qossmic\Deptrac\Ast\AstMap\TokenInterface;

final class ClassLikeType implements TokenInterface
{
    public const TYPE_CLASSLIKE = 0;
    public const TYPE_CLASS = 1;
    public const TYPE_INTERFACE = 2;
    public const TYPE_TRAIT = 4;

    private const TYPE_MAPPING = [
        'class' => self::TYPE_CLASS,
        'interface' => self::TYPE_INTERFACE,
        'trait' => self::TYPE_TRAIT,
    ];

    private int $type;

    private function __construct(int $type)
    {
        $this->type = $type;
    }

    public static function fromString(string $type): self
    {
        $mappedValue = self::TYPE_MAPPING[$type] ?? null;

        if (null === $mappedValue) {
            throw new LogicException(sprintf('The type %s is not supported.', $type));
        }

        return new self($mappedValue);
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
        return $this->toInt() === $type->toInt();
    }

    public function toInt(): int
    {
        return $this->type;
    }

    public function toString(): string
    {
        return array_flip(self::TYPE_MAPPING)[$this->type];
    }
}
