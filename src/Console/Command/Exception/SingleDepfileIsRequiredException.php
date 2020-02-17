<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Console\Command\Exception;

final class SingleDepfileIsRequiredException extends \RuntimeException
{
    /**
     * @param mixed $argument
     */
    public static function fromArgument($argument): self
    {
        return new self(sprintf(
            'Please specify a path to a depfile. Got "%s".',
            gettype($argument)
        ));
    }
}
