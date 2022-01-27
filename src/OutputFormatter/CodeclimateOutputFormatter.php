<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use Exception;
use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\Rule;
use Qossmic\Deptrac\RulesetEngine\SkippedViolation;
use Qossmic\Deptrac\RulesetEngine\Uncovered;
use Qossmic\Deptrac\RulesetEngine\Violation;
use function json_encode;
use function json_last_error;
use function sprintf;
use const JSON_PRETTY_PRINT;

final class CodeclimateOutputFormatter implements OutputFormatterInterface
{
    public static function getName(): string
    {
        return 'codeclimate';
    }

    public static function getConfigName(): string
    {
        return self::getName();
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function finish(
        Context $context,
        Output $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $violations = [];
        foreach ($context->rules() as $rule) {
            if (!$rule instanceof Violation && !$rule instanceof SkippedViolation && !$rule instanceof Uncovered) {
                continue;
            }

            if (!($outputFormatterInput->getReportSkipped()) && $rule instanceof SkippedViolation) {
                continue;
            }

            if (!($outputFormatterInput->getReportUncovered()) && $rule instanceof Uncovered) {
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

        $json = json_encode($violations, JSON_PRETTY_PRINT);

        if (false === $json) {
            throw new Exception(sprintf('Unable to render codeclimate output. %s', $this->jsonLastError()));
        }

        $dumpJsonPath = $outputFormatterInput->getOutputPath();
        if (null !== $dumpJsonPath) {
            file_put_contents($dumpJsonPath, $json);
            $output->writeLineFormatted('<info>Codeclimate Report dumped to '.realpath($dumpJsonPath).'</info>');

            return;
        }

        $output->writeRaw($json);
    }

    /**
     * @param array<array{type: string, check_name: string, description: string, categories: array<string>, severity: string, location: array{path: string, lines: array{begin: int, end: int}}}> $violationsArray
     */
    private function addFailure(array &$violationsArray, Violation $violation): void
    {
        $violationsArray[] = $this->buildRuleArray($violation, $this->getFailureMessage($violation), 'major');
    }

    private function getFailureMessage(Violation $violation): string
    {
        $dependency = $violation->getDependency();

        return sprintf(
            '%s must not depend on %s (%s on %s)',
            $dependency->getDependant()->toString(),
            $dependency->getDependee()->toString(),
            $violation->getDependantLayerName(),
            $violation->getDependeeLayerName()
        );
    }

    /**
     * @param array<array{type: string, check_name: string, description: string, categories: array<string>, severity: string, location: array{path: string, lines: array{begin: int, end: int}}}> $violationsArray
     */
    private function addSkipped(array &$violationsArray, SkippedViolation $violation): void
    {
        $violationsArray[] = $this->buildRuleArray($violation, $this->getWarningMessage($violation), 'minor');
    }

    private function getWarningMessage(SkippedViolation $violation): string
    {
        $dependency = $violation->getDependency();

        return sprintf(
            '%s should not depend on %s (%s on %s)',
            $dependency->getDependant()->toString(),
            $dependency->getDependee()->toString(),
            $violation->getDependantLayerName(),
            $violation->getDependeeLayerName()
        );
    }

    /**
     * @param array<array{type: string, check_name: string, description: string, categories: array<string>, severity: string, location: array{path: string, lines: array{begin: int, end: int}}}> $violationsArray
     */
    private function addUncovered(array &$violationsArray, Uncovered $violation): void
    {
        $violationsArray[] = $this->buildRuleArray($violation, $this->getUncoveredMessage($violation), 'info');
    }

    private function getUncoveredMessage(Uncovered $violation): string
    {
        $dependency = $violation->getDependency();

        return sprintf(
            '%s has uncovered dependency on %s (%s)',
            $dependency->getDependant()->toString(),
            $dependency->getDependee()->toString(),
            $violation->getLayer()
        );
    }

    private function jsonLastError(): string
    {
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return 'No errors';
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
            default:
                return 'Unknown error';
        }
    }

    /**
     * @return array{type: string, check_name: string, description: string, categories: array<string>, severity: string, location: array{path: string, lines: array{begin: int, end: int}}} $violationsArray
     */
    private function buildRuleArray(Rule $rule, string $message, string $severity): array
    {
        return [
            'type' => 'issue',
            'check_name' => 'Dependency violation',
            'description' => $message,
            'categories' => ['Style', 'Complexity'],
            'severity' => $severity,
            'location' => [
                'path' => $rule->getDependency()->getFileOccurrence()->getFilepath(),
                'lines' => [
                    'begin' => $rule->getDependency()->getFileOccurrence()->getLine(),
                    'end' => $rule->getDependency()->getFileOccurrence()->getLine(),
                ],
            ],
        ];
    }
}
