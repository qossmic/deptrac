<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser;

use Qossmic\Deptrac\Supportive\DependencyInjection\EmitterTypes;

/**
 * @psalm-immutable
 */
class TokenType
{
    final public const CLASS_LIKE = 'class-like';
    final public const FUNCTION = 'function';
    final public const FILE = 'file';

    /** @param 'class-like'|'function'|'file' $value */
    private function __construct(public string $value)
    {
    }

    /**
     * @return array{'class-like', 'function', 'file'}
     */
    public static function values(): array
    {
        return [
            self::CLASS_LIKE,
            self::FUNCTION,
            self::FILE,
        ];
    }

    public static function from(string $value): self
    {
        if (!in_array($value, self::values(), true)) {
            throw InvalidTokenException::invalidTokenType($value, self::values());
        }

        return new self($value);
    }

    public static function tryFromEmitterType(string $emitterType): ?self
    {
        if (EmitterTypes::CLASS_TOKEN === $emitterType) {
            $emitterType = self::CLASS_LIKE;
        }

        try {
            return self::from($emitterType);
        } catch (InvalidTokenException) {
            return null;
        }
    }
}
