<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\OutputFormatter;

use Exception;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInterface;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use Qossmic\Deptrac\Contract\Result\LegacyResult;
use Qossmic\Deptrac\Contract\Result\SkippedViolation;
use Qossmic\Deptrac\Contract\Result\Uncovered;
use Qossmic\Deptrac\Contract\Result\Violation;

use function json_encode;
use function json_last_error;
use function sprintf;

use const JSON_PRETTY_PRINT;

/**
 * @internal
 */
final class JsonOutputFormatter implements OutputFormatterInterface
{
    public static function getName(): string
    {
        return 'json';
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function finish(
        LegacyResult $result,
        OutputInterface $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $jsonArray = [];
        $violations = [];
        foreach ($result->rules as $rule) {
            if (!$rule instanceof Violation && !$rule instanceof SkippedViolation && !$rule instanceof Uncovered) {
                continue;
            }

            if (!$outputFormatterInput->reportSkipped && $rule instanceof SkippedViolation) {
                continue;
            }

            if (!$outputFormatterInput->reportUncovered && $rule instanceof Uncovered) {
                continue;
            }

            switch (true) {
                case $rule instanceof Violation:
                    $this->addFailure($violations, $rule);
                    break;
                case $rule instanceof SkippedViolation:
                    $this->addSkipped($violations, $rule);
                    break;
                case $rule instanceof Uncovered:
                    $this->addUncovered($violations, $rule);
                    break;
            }
        }

        $jsonArray['Report'] = [
            'Violations' => count($result->violations()),
            'Skipped violations' => count($result->skippedViolations()),
            'Uncovered' => count($result->uncovered()),
            'Allowed' => count($result->allowed()),
            'Warnings' => count($result->warnings),
            'Errors' => count($result->errors),
        ];

        foreach ($violations as &$value) {
            $value['violations'] = count($value['messages']);
        }

        $jsonArray['files'] = $violations;
        $json = json_encode($jsonArray, JSON_PRETTY_PRINT);

        if (false === $json) {
            throw new Exception(sprintf('Unable to render json output. %s', $this->jsonLastError()));
        }

        $dumpJsonPath = $outputFormatterInput->outputPath;
        if (null !== $dumpJsonPath) {
            file_put_contents($dumpJsonPath, $json);
            $output->writeLineFormatted('<info>JSON Report dumped to '.realpath($dumpJsonPath).'</info>');

            return;
        }

        $output->writeRaw($json);
    }

    /**
     * @param array<string, array{messages: array<int, array{message: string, line: int, type: string}>}> $violationsArray
     */
    private function addFailure(array &$violationsArray, Violation $violation): void
    {
        $className = $violation->getDependency()->getFileOccurrence()->filepath;

        $violationsArray[$className]['messages'][] = [
            'message' => $this->getFailureMessage($violation),
            'line' => $violation->getDependency()->getFileOccurrence()->line,
            'type' => 'error',
        ];
    }

    private function getFailureMessage(Violation $violation): string
    {
        $dependency = $violation->getDependency();

        return sprintf(
            '%s must not depend on %s (%s on %s)',
            $dependency->getDepender()->toString(),
            $dependency->getDependent()->toString(),
            $violation->getDependerLayer(),
            $violation->getDependentLayer()
        );
    }

    /**
     * @param array<string, array{messages: array<int, array{message: string, line: int, type: string}>}> $violationsArray
     */
    private function addSkipped(array &$violationsArray, SkippedViolation $violation): void
    {
        $className = $violation->getDependency()->getFileOccurrence()->filepath;

        $violationsArray[$className]['messages'][] = [
            'message' => $this->getWarningMessage($violation),
            'line' => $violation->getDependency()->getFileOccurrence()->line,
            'type' => 'warning',
        ];
    }

    private function getWarningMessage(SkippedViolation $violation): string
    {
        $dependency = $violation->getDependency();

        return sprintf(
            '%s should not depend on %s (%s on %s)',
            $dependency->getDepender()->toString(),
            $dependency->getDependent()->toString(),
            $violation->getDependerLayer(),
            $violation->getDependentLayer()
        );
    }

    /**
     * @param array<string, array{messages: array<int, array{message: string, line: int, type: string}>}> $violationsArray
     */
    private function addUncovered(array &$violationsArray, Uncovered $violation): void
    {
        $className = $violation->getDependency()->getFileOccurrence()->filepath;

        $violationsArray[$className]['messages'][] = [
            'message' => $this->getUncoveredMessage($violation),
            'line' => $violation->getDependency()->getFileOccurrence()->line,
            'type' => 'warning',
        ];
    }

    private function getUncoveredMessage(Uncovered $violation): string
    {
        $dependency = $violation->getDependency();

        return sprintf(
            '%s has uncovered dependency on %s (%s)',
            $dependency->getDepender()->toString(),
            $dependency->getDependent()->toString(),
            $violation->layer
        );
    }

    private function jsonLastError(): string
    {
        return match (json_last_error()) {
            JSON_ERROR_NONE => 'No errors',
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
            default => 'Unknown error',
        };
    }
}
