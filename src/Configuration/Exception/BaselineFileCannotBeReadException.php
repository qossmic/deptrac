<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Configuration\Exception;

final class BaselineFileCannotBeReadException extends \RuntimeException
{
    public static function fromFilename(string $filename): self
    {
        return new self(sprintf(
            'Defined import file "%s" cannot be read.',
            $filename
        ));
    }
}
