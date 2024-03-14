<?php

declare (strict_types=1);
namespace DEPTRAC_202403\PhpParser\ErrorHandler;

use DEPTRAC_202403\PhpParser\Error;
use DEPTRAC_202403\PhpParser\ErrorHandler;
/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
class Throwing implements ErrorHandler
{
    public function handleError(Error $error)
    {
        throw $error;
    }
}
