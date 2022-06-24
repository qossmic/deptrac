<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\Dependency\InheritDependency;
use Qossmic\Deptrac\Result\Allowed;
use Qossmic\Deptrac\Result\Error;
use Qossmic\Deptrac\Result\LegacyResult;
use Qossmic\Deptrac\Result\Rule;
use Qossmic\Deptrac\Result\SkippedViolation;
use Qossmic\Deptrac\Result\Uncovered;
use Qossmic\Deptrac\Result\Violation;
use Qossmic\Deptrac\Result\Warning;
use Symfony\Component\Console\Helper\TableSeparator;
use function count;

final class TableOutputFormatter implements OutputFormatterInterface
{
    public static function getName(): string
    {
        return 'table';
    }

    public function finish(
        LegacyResult $result,
        Output $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $groupedRules = [];
        foreach ($result->rules() as $rule) {
            if ($rule instanceof Allowed) {
                continue;
            }

            if ($rule instanceof Violation || ($outputFormatterInput->getReportSkipped() && $rule instanceof SkippedViolation)) {
                $groupedRules[$rule->getDependerLayer()][] = $rule;
            } elseif ($outputFormatterInput->getReportUncovered() && $rule instanceof Uncovered) {
                $groupedRules[$rule->getLayer()][] = $rule;
            }
        }

        $style = $output->getStyle();

        foreach ($groupedRules as $layer => $rules) {
            $rows = [];
            foreach ($rules as $rule) {
                if ($rule instanceof Uncovered) {
                    $rows[] = $this->uncoveredRow($rule, $outputFormatterInput->getFailOnUncovered());
                } else {
                    $rows[] = $this->violationRow($rule);
                }
            }

            $style->table(['Reason', $layer], $rows);
        }

        if ($result->hasErrors()) {
            $this->printErrors($result, $output);
        }

        if ($result->hasWarnings()) {
            $this->printWarnings($result, $output);
        }

        $this->printSummary($result, $output, $outputFormatterInput->getFailOnUncovered());
    }

    /**
     * @param Violation|SkippedViolation $rule
     *
     * @return array{string, string}
     */
    private function violationRow(Rule $rule): array
    {
        $dependency = $rule->getDependency();

        $message = sprintf(
            '<info>%s</info> must not depend on <info>%s</info> (%s)',
            $dependency->getDepender()->toString(),
            $dependency->getDependent()->toString(),
            $rule->getDependentLayer()
        );

        if ($dependency instanceof InheritDependency) {
            $message .= "\n".$this->formatInheritPath($dependency);
        }

        $fileOccurrence = $rule->getDependency()->getFileOccurrence();
        $message .= sprintf("\n%s:%d", $fileOccurrence->getFilepath(), $fileOccurrence->getLine());

        return [
            $rule instanceof SkippedViolation ? '<fg=yellow>Skipped</>' : '<fg=red>Violation</>',
            $message,
        ];
    }

    private function formatInheritPath(InheritDependency $dependency): string
    {
        $buffer = [];
        $astInherit = $dependency->getInheritPath();
        foreach ($astInherit->getPath() as $p) {
            array_unshift($buffer, sprintf('%s::%d', $p->getClassLikeName()->toString(), $p->getFileOccurrence()->getLine()));
        }

        $buffer[] = sprintf('%s::%d', $astInherit->getClassLikeName()->toString(), $astInherit->getFileOccurrence()->getLine());
        $buffer[] = sprintf(
            '%s::%d',
            $dependency->getOriginalDependency()->getDependent()->toString(),
            $dependency->getOriginalDependency()->getFileOccurrence()->getLine()
        );

        return implode(" -> \n", $buffer);
    }

    private function printSummary(LegacyResult $result, Output $output, bool $reportUncoveredAsError): void
    {
        $violationCount = count($result->violations());
        $skippedViolationCount = count($result->skippedViolations());
        $uncoveredCount = count($result->uncovered());
        $allowedCount = count($result->allowed());
        $warningsCount = count($result->warnings());
        $errorsCount = count($result->errors());

        $uncoveredFg = $reportUncoveredAsError ? 'red' : 'yellow';

        $style = $output->getStyle();
        $style->newLine();
        $style->definitionList(
            'Report',
            new TableSeparator(),
            ['Violations' => sprintf('<fg=%s>%d</>', $violationCount > 0 ? 'red' : 'default', $violationCount)],
            ['Skipped violations' => sprintf('<fg=%s>%d</>', $skippedViolationCount > 0 ? 'yellow' : 'default', $skippedViolationCount)],
            ['Uncovered' => sprintf('<fg=%s>%d</>', $uncoveredCount > 0 ? $uncoveredFg : 'default', $uncoveredCount)],
            ['Allowed' => $allowedCount],
            ['Warnings' => sprintf('<fg=%s>%d</>', $warningsCount > 0 ? 'yellow' : 'default', $warningsCount)],
            ['Errors' => sprintf('<fg=%s>%d</>', $errorsCount > 0 ? 'red' : 'default', $errorsCount)]
        );
    }

    /**
     * @return array{string, string}
     */
    private function uncoveredRow(Uncovered $rule, bool $reportAsError): array
    {
        $dependency = $rule->getDependency();

        $message = sprintf(
            '<info>%s</info> has uncovered dependency on <info>%s</info>',
            $dependency->getDepender()->toString(),
            $dependency->getDependent()->toString()
        );

        if ($dependency instanceof InheritDependency) {
            $message .= "\n".$this->formatInheritPath($dependency);
        }

        $fileOccurrence = $rule->getDependency()->getFileOccurrence();
        $message .= sprintf("\n%s:%d", $fileOccurrence->getFilepath(), $fileOccurrence->getLine());

        return [
            sprintf('<fg=%s>Uncovered</>', $reportAsError ? 'red' : 'yellow'),
            $message,
        ];
    }

    private function printErrors(LegacyResult $result, Output $output): void
    {
        $output->getStyle()->table(
            ['<fg=red>Errors</>'],
            array_map(
                static function (Error $error) {
                    return [$error->toString()];
                },
                $result->errors()
            )
        );
    }

    private function printWarnings(LegacyResult $result, Output $output): void
    {
        $output->getStyle()->table(
            ['<fg=yellow>Warnings</>'],
            array_map(
                static function (Warning $warning) {
                    return [$warning->toString()];
                },
                $result->warnings()
            )
        );
    }
}
