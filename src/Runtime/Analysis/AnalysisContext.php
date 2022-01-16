<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Runtime\Analysis;

use Qossmic\Deptrac\Exception\Runtime\AnalysisContextException;
use function array_diff;
use function array_unique;
use function array_values;

final class AnalysisContext
{
    private const SUPPORTED_TYPES = [
        self::CLASS_TOKEN,
        self::CLASS_SUPERGLOBAL_TOKEN,
        self::USE_TOKEN,
        self::FILE_TOKEN,
        self::FUNCTION_TOKEN,
        self::FUNCTION_SUPERGLOBAL_TOKEN,
    ];

    public const CLASS_TOKEN = 'class';
    public const CLASS_SUPERGLOBAL_TOKEN = 'class_superglobal';
    public const USE_TOKEN = 'use';
    public const FILE_TOKEN = 'file';
    public const FUNCTION_TOKEN = 'function';
    public const FUNCTION_SUPERGLOBAL_TOKEN = 'function_superglobal';

    /** @var string[] */
    private array $types;

    /**
     * @param string[] $types
     */
    public function __construct(array $types)
    {
        if ([] === $types) {
            throw AnalysisContextException::noTypesProvided(self::SUPPORTED_TYPES);
        }

        $unsupportedTypes = array_diff($types, self::SUPPORTED_TYPES);
        if ([] !== $unsupportedTypes) {
            throw AnalysisContextException::unsupportedTypes($unsupportedTypes, self::SUPPORTED_TYPES);
        }

        $this->types = array_values(array_unique($types));
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}
