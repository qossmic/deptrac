<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use RuntimeException;

final class ShouldNotHappenException extends RuntimeException
{
    public function __construct(string $message = 'Internal error.')
    {
        parent::__construct($message);
    }
}
