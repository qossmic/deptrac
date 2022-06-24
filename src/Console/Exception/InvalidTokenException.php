<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Exception;

use Qossmic\Deptrac\Utils\ExceptionInterface;
use RuntimeException;
use function implode;

final class InvalidTokenException extends RuntimeException implements ExceptionInterface
{
    /**
     * @param string[] $allowedTypes
     */
    public static function invalidTokenType(string $tokenType, array $allowedTypes): self
    {
        return new self(sprintf(
            'Invalid token type "%s". Only "%s" are supported.',
            $tokenType,
            implode('", "', $allowedTypes)
        ));
    }
}
