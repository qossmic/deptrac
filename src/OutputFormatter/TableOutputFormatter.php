<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\Dependency\InheritDependency;
use Qossmic\Deptrac\RulesetEngine\Allowed;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\Error;
use Qossmic\Deptrac\RulesetEngine\Rule;
use Qossmic\Deptrac\RulesetEngine\SkippedViolation;
use Qossmic\Deptrac\RulesetEngine\Uncovered;
use Qossmic\Deptrac\RulesetEngine\Violation;
use Qossmic\Deptrac\RulesetEngine\Warning;
use Symfony\Component\Console\Helper\TableSeparator;
use function count;

final class TableOutputFormatter implements OutputFormatterInterface
{
    public static function getName(): string
    {
        return 'table';
    }

    public static function getConfigName(): string
    {
        return self::getName();
    }

    public function finish(
        Context $context,
        Output $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $groupedRules = [];
        foreach ($context->rules() as $rule) {
            if ($rule instanceof Allowed) {
                continue;
            }

            if ($rule instanceof Violation || ($outputFormatterInput->getReportSkipped() && $rule instanceof SkippedViolation)) {
                $groupedRules[$rule->getDependantLayerName()][] = $rule;
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

        if ($context->hasErrors()) {
            $this->printErrors($context, $output);
        }

        if ($context->hasWarnings()) {
            $this->printWarnings($context, $output);
        }

        $this->printSummary($context, $output, $outputFormatterInput->getFailOnUncovered());
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
            $dependency->getDependant()->toString(),
            $dependency->getDependee()->toString(),
            $rule->getDependeeLayerName()
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
            $dependency->getOriginalDependency()->getDependee()->toString(),
            $dependency->getOriginalDependency()->getFileOccurrence()->getLine()
        );

        return implode(" -> \n", $buffer);
    }

    private function printSummary(Context $context, Output $output, bool $reportUncoveredAsError): void
    {
        $violationCount = count($context->violations());
        $skippedViolationCount = count($context->skippedViolations());
        $uncoveredCount = count($context->uncovered());
        $allowedCount = count($context->allowed());
        $warningsCount = count($context->warnings());
        $errorsCount = count($context->errors());

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
            $dependency->getDependant()->toString(),
            $dependency->getDependee()->toString()
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

    private function printErrors(Context $context, Output $output): void
    {
        $output->getStyle()->table(
            ['<fg=red>Errors</>'],
            array_map(
                static function (Error $error) {
                    return [$error->toString()];
                },
                $context->errors()
            )
        );
    }

    private function printWarnings(Context $context, Output $output): void
    {
        $output->getStyle()->table(
            ['<fg=yellow>Warnings</>'],
            array_map(
                static function (Warning $warning) {
                    return [$warning->toString()];
                },
                $context->warnings()
            )
        );
    }
}
