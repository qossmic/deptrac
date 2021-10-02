<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Exception;

use RuntimeException;

final class ShouldNotHappenException extends RuntimeException implements ExceptionInterface
{
    public function __construct(string $message = 'Internal error.')
    {
        parent::__construct($message);
    }
}
