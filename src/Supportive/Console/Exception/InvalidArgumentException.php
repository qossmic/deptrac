<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Console\Exception;

use Qossmic\Deptrac\Contract\ExceptionInterface;
use RuntimeException;

final class InvalidArgumentException extends RuntimeException implements ExceptionInterface
{
    public static function invalidDepfileType(mixed $argument): self
    {
        return new self(sprintf(
            'Please specify a path to a Depfile. Got "%s".',
            gettype($argument)
        ));
    }
}
