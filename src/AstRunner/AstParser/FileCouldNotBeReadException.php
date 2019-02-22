<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

class FileCouldNotBeReadException extends \RuntimeException
{
    public function __construct(string $filepath)
    {
        parent::__construct(sprintf('"%s" could not be read.', $filepath));
    }
}
