<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Exception;

use Qossmic\Deptrac\Utils\ExceptionInterface;
use RuntimeException;

final class AnalyseException extends RuntimeException implements ExceptionInterface
{
    public static function invalidFormatter(): self
    {
        return new self('Invalid output formatter selected.');
    }

    public static function finishedWithUncovered(): self
    {
        return new self('Analysis finished, but contains uncovered tokens.');
    }

    public static function finishedWithViolations(): self
    {
        return new self('Analysis finished, but contains ruleset violations.');
    }

    public static function failedWithErrors(): self
    {
        return new self('Analysis failed, due to an error.');
    }
}
