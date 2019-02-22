<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

class FileNotExistsException extends \RuntimeException
{
    public function __construct(string $filepath)
    {
        parent::__construct(sprintf('"%s" is not a valid path or does not exists.', $filepath));
    }
}
