<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac;

final class ShouldNotHappenException extends \RuntimeException
{
    public function __construct(string $message = 'Internal error.')
    {
        parent::__construct($message);
    }
}
