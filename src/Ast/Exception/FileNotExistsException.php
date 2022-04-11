<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\Exception;

use Qossmic\Deptrac\Exception\ExceptionInterface;
use RuntimeException;

class FileNotExistsException extends RuntimeException implements ExceptionInterface
{
    public function __construct(string $filepath)
    {
        parent::__construct(sprintf('"%s" is not a valid path or does not exists.', $filepath));
    }
}
