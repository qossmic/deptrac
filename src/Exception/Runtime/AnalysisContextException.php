<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Exception\Runtime;

use Qossmic\Deptrac\Exception\ExceptionInterface;
use RuntimeException;
use function implode;
use function sprintf;

final class AnalysisContextException extends RuntimeException implements ExceptionInterface
{
    /**
     * @param string[] $supportedTypes
     */
    public static function noTypesProvided(array $supportedTypes): self
    {
        return new self(sprintf(
            'You must provide at least one type to be analysed. Supported types: "%s".',
            implode('", "', $supportedTypes)
        ));
    }

    /**
     * @param string[] $unsupportedTypes
     * @param string[] $supportedTypes
     */
    public static function unsupportedTypes(array $unsupportedTypes, array $supportedTypes): self
    {
        return new self(sprintf(
            'Your analysis context contains invalid types: "%s". Supported types: "%s".',
            implode('", "', $unsupportedTypes),
            implode('", "', $supportedTypes)
        ));
    }
}
