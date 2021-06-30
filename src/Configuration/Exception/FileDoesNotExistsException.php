<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration\Exception;

use RuntimeException;

final class FileDoesNotExistsException extends RuntimeException
{
    public static function fromFilename(string $filename): self
    {
        return new self(sprintf(
            'File "%s" does not exist. Run "deptrac init" to create one.',
            $filename
        ));
    }
}
