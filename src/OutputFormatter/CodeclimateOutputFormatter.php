<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use Exception;
use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\OutputFormatter\Configuration\ConfigurationCodeclimate;
use Qossmic\Deptrac\OutputFormatter\Configuration\FormatterConfiguration;
use Qossmic\Deptrac\Result\LegacyResult;
use Qossmic\Deptrac\Result\Rule;
use Qossmic\Deptrac\Result\SkippedViolation;
use Qossmic\Deptrac\Result\Uncovered;
use Qossmic\Deptrac\Result\Violation;

use function json_encode;
use function json_last_error;
use function sprintf;

use const JSON_PRETTY_PRINT;

/**
 * @internal
 */
final class CodeclimateOutputFormatter implements OutputFormatterInterface
{
    /**
     * @var array{severity?: array{failure?: string, skipped?: string, uncovered?: string}}
     */
    private array $config;

    public function __construct(FormatterConfiguration $config)
    {
        /** @var array{severity?: array{failure?: string, skipped?: string, uncovered?: string}} $extractedConfig */
        $extractedConfig = $config->getConfigFor('codeclimate');
        $this->config = $extractedConfig;
    }

    public static function getName(): string
    {
        return 'codeclimate';
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function finish(
        LegacyResult $result,
        Output $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $formatterConfig = ConfigurationCodeclimate::fromArray($this->config);

        $violations = [];
        foreach ($result->rules() as $rule) {
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
                    $this->addFailure($violations, $rule, $formatterConfig);
                    break;
                case $rule instanceof SkippedViolation:
                    $this->addSkipped($violations, $rule, $formatterConfig);
                    break;
                case $rule instanceof Uncovered:
                    $this->addUncovered($violations, $rule, $formatterConfig);
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
     * @param array<array{type: string, check_name: string, fingerprint: string, description: string, categories: array<string>, severity: string, location: array{path: string, lines: array{begin: int}}}> $violationsArray
     */
    private function addFailure(array &$violationsArray, Violation $violation, ConfigurationCodeclimate $config): void
    {
        $violationsArray[] = $this->buildRuleArray(
            $violation,
            $this->getFailureMessage($violation),
            $config->getSeverity('failure') ?? 'major'
        );
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
     * @param array<array{type: string, check_name: string, fingerprint: string, description: string, categories: array<string>, severity: string, location: array{path: string, lines: array{begin: int}}}> $violationsArray
     */
    private function addSkipped(array &$violationsArray, SkippedViolation $violation, ConfigurationCodeclimate $config): void
    {
        $violationsArray[] = $this->buildRuleArray(
            $violation,
            $this->getWarningMessage($violation),
            $config->getSeverity('skipped') ?? 'minor'
        );
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
     * @param array<array{type: string, check_name: string, fingerprint: string, description: string, categories: array<string>, severity: string, location: array{path: string, lines: array{begin: int}}}> $violationsArray
     */
    private function addUncovered(array &$violationsArray, Uncovered $violation, ConfigurationCodeclimate $config): void
    {
        $violationsArray[] = $this->buildRuleArray(
            $violation,
            $this->getUncoveredMessage($violation),
            $config->getSeverity('uncovered') ?? 'info'
        );
    }

    private function getUncoveredMessage(Uncovered $violation): string
    {
        $dependency = $violation->getDependency();

        return sprintf(
            '%s has uncovered dependency on %s (%s)',
            $dependency->getDepender()->toString(),
            $dependency->getDependent()->toString(),
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
     * @return array{type: string, check_name: string, fingerprint: string, description: string, categories: array<string>, severity: string, location: array{path: string, lines: array{begin: int}}}
     */
    private function buildRuleArray(Rule $rule, string $message, string $severity): array
    {
        return [
            'type' => 'issue',
            'check_name' => 'Dependency violation',
            'fingerprint' => $this->buildFingerprint($rule),
            'description' => $message,
            'categories' => ['Style', 'Complexity'],
            'severity' => $severity,
            'location' => [
                'path' => $rule->getDependency()->getFileOccurrence()->getFilepath(),
                'lines' => [
                    'begin' => $rule->getDependency()->getFileOccurrence()->getLine(),
                ],
            ],
        ];
    }

    private function buildFingerprint(Rule $rule): string
    {
        return sha1(implode(',', [
            get_class($rule),
            $rule->getDependency()->getDepender()->toString(),
            $rule->getDependency()->getDependent()->toString(),
            $rule->getDependency()->getFileOccurrence()->getFilepath(),
            $rule->getDependency()->getFileOccurrence()->getLine(),
        ]));
    }
}
