<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Console\Exception;

use Qossmic\Deptrac\Contract\ExceptionInterface;
use RuntimeException;
use function sprintf;

final class InvalidLayerException extends RuntimeException implements ExceptionInterface
{
    public static function unknownLayer(?string $layer): self
    {
        if (null === $layer) {
            return new self('No layer provided.');
        }

        return new self(sprintf('Layer "%s" not found.', $layer));
    }
}
