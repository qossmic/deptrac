<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Exception;

use Qossmic\Deptrac\Utils\ExceptionInterface;
use RuntimeException;

final class InvalidArgumentException extends RuntimeException implements ExceptionInterface
{
    /**
     * @param mixed $argument
     */
    public static function invalidDepfileType($argument): self
    {
        return new self(sprintf(
            'Please specify a path to a Depfile. Got "%s".',
            gettype($argument)
        ));
    }
}
