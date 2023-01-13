<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Console\Exception;

use Qossmic\Deptrac\Contract\ExceptionInterface;
use Qossmic\Deptrac\Core\Dependency\InvalidEmitterConfiguration;
use Qossmic\Deptrac\Core\Dependency\UnrecognizedTokenException;
use Qossmic\Deptrac\Core\InputCollector\InputException;
use Qossmic\Deptrac\Core\Layer\Exception\InvalidLayerDefinitionException;
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

    public static function invalidEmitterConfiguration(InvalidEmitterConfiguration $e): self
    {
        return new self('Invalid emitter configuration.', 0, $e);
    }

    public static function unrecognizedToken(UnrecognizedTokenException $e): self
    {
        return new self('Unrecognized token.', 0, $e);
    }

    public static function invalidLayerDefinition(InvalidLayerDefinitionException $e): self
    {
        return new self('Invalid layer definition.', 0, $e);
    }

    public static function invalidFileInput(InputException $e): self
    {
        return new self('Invalid file input', 0, $e);
    }
}
