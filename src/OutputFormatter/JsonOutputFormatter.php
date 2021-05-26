<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use function json_encode;
use Qossmic\Deptrac\Console\Command\AnalyzeCommand;
use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\SkippedViolation;
use Qossmic\Deptrac\RulesetEngine\Uncovered;
use Qossmic\Deptrac\RulesetEngine\Violation;

final class JsonOutputFormatter implements OutputFormatterInterface
{
    public const DUMP_JSON = 'json-dump';

    public function getName(): string
    {
        return 'json';
    }

    /**
     * @return OutputFormatterOption[]
     */
    public function configureOptions(): array
    {
        return [
            OutputFormatterOption::newValueOption(self::DUMP_JSON, 'path to a dumped json file'),
        ];
    }

    public function enabledByDefault(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function finish(
        Context $context,
        Output $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $reportSkipped = $outputFormatterInput->getOptionAsBoolean(AnalyzeCommand::OPTION_REPORT_SKIPPED);
        $reportUncovered = $outputFormatterInput->getOptionAsBoolean(AnalyzeCommand::OPTION_REPORT_UNCOVERED);

        $jsonArray = [];
        $jsonArray['files'] = [];
        foreach ($context->rules() as $rule) {
            if (!$rule instanceof Violation && !$rule instanceof SkippedViolation && !$rule instanceof Uncovered) {
                continue;
            }

            if (!$reportSkipped && $rule instanceof SkippedViolation) {
                continue;
            }

            if (!$reportUncovered && $rule instanceof Uncovered) {
                continue;
            }

            switch (true) {
                case $rule instanceof Violation:
                    $this->addFailure($jsonArray['files'], $rule);
                    break;
                case $rule instanceof SkippedViolation:
                    $this->addSkipped($jsonArray['files'], $rule);
                    break;
                case $rule instanceof Uncovered:
                    $this->addUncovered($jsonArray['files'], $rule);
                    break;
            }
        }

        $jsonArray['Report'] = [
            'Violations' => count($context->violations()),
            'Skipped violations' => count($context->skippedViolations()),
            'Uncovered' => count($context->uncovered()),
            'Allowed' => count($context->allowed()),
            'Warnings' => count($context->warnings()),
            'Errors' => count($context->errors()),
        ];

        $json = json_encode($jsonArray);

        $dumpJsonPath = (string) $outputFormatterInput->getOption(self::DUMP_JSON);
        if ($dumpJsonPath) {
            file_put_contents($dumpJsonPath, $json);
            $output->writeLineFormatted('<info>JSON Report dumped to '.realpath($dumpJsonPath).'</info>');

            return;
        }

        $output->writeRaw((string) $json);
    }

    /**
     * @param array<string, array<string, array<int, array<string, string|int>>>> $violationsArray
     * @param-out non-empty-array<string, non-empty-array<string, 0|array<int, non-empty-array<string, int|string>>|positive-int>> $violationsArray
     */
    private function addFailure(array &$violationsArray, Violation $violation): void
    {
        $className = $violation->getDependency()->getFileOccurrence()->getFilepath();

        $violationsArray[$className]['messages'][] = [
            'message' => $this->getFailureMessage($violation),
            'line' => $violation->getDependency()->getFileOccurrence()->getLine(),
            'type' => 'error',
        ];

        $violationsArray[$className]['violations'] = count($violationsArray[$className]['messages']);
    }

    private function getFailureMessage(Violation $violation): string
    {
        $dependency = $violation->getDependency();

        return sprintf(
            '%s must not depend on %s (%s on %s)',
            $dependency->getClassLikeNameA()->toString(),
            $dependency->getClassLikeNameB()->toString(),
            $violation->getLayerA(),
            $violation->getLayerB()
        );
    }

    /**
     * @param array<string, array<string, array<int, array<string, string|int>>>> $violationsArray
     * @param-out non-empty-array<string, non-empty-array<string, 0|array<int, non-empty-array<string, int|string>>|positive-int>> $violationsArray
     */
    private function addSkipped(array &$violationsArray, SkippedViolation $violation): void
    {
        $className = $violation->getDependency()->getFileOccurrence()->getFilepath();

        $violationsArray[$className]['messages'][] = [
            'message' => $this->getWarningMessage($violation),
            'line' => $violation->getDependency()->getFileOccurrence()->getLine(),
            'type' => 'warning',
        ];

        $violationsArray[$className]['violations'] = count($violationsArray[$className]['messages']);
    }

    private function getWarningMessage(SkippedViolation $violation): string
    {
        $dependency = $violation->getDependency();

        return sprintf(
            '%s should not depend on %s (%s on %s)',
            $dependency->getClassLikeNameA()->toString(),
            $dependency->getClassLikeNameB()->toString(),
            $violation->getLayerA(),
            $violation->getLayerB()
        );
    }

    /**
     * @param array<string, array<string, array<int, array<string, string|int>>>> $violationsArray
     * @param-out non-empty-array<string, non-empty-array<string, 0|array<int, non-empty-array<string, int|string>>|positive-int>> $violationsArray
     */
    private function addUncovered(array &$violationsArray, Uncovered $violation): void
    {
        $className = $violation->getDependency()->getFileOccurrence()->getFilepath();

        $violationsArray[$className]['messages'][] = [
            'message' => $this->getUncoveredMessage($violation),
            'line' => $violation->getDependency()->getFileOccurrence()->getLine(),
            'type' => 'warning',
        ];

        $violationsArray[$className]['violations'] = count($violationsArray[$className]['messages']);
    }

    private function getUncoveredMessage(Uncovered $violation): string
    {
        $dependency = $violation->getDependency();

        return sprintf(
            '%s has uncovered dependency on %s (%s)',
            $dependency->getClassLikeNameA()->toString(),
            $dependency->getClassLikeNameB()->toString(),
            $violation->getLayer()
        );
    }
}
