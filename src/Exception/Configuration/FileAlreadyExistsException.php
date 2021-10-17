<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Exception\Configuration;

use Qossmic\Deptrac\Exception\ExceptionInterface;
use RuntimeException;

final class FileAlreadyExistsException extends RuntimeException implements ExceptionInterface
{
}
