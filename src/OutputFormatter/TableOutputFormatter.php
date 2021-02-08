<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use Qossmic\Deptrac\Console\Command\AnalyzeCommand;
use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\Dependency\InheritDependency;
use Qossmic\Deptrac\RulesetEngine\Allowed;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\Error;
use Qossmic\Deptrac\RulesetEngine\Rule;
use Qossmic\Deptrac\RulesetEngine\SkippedViolation;
use Qossmic\Deptrac\RulesetEngine\Uncovered;
use Qossmic\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Console\Helper\TableSeparator;

final class TableOutputFormatter implements OutputFormatterInterface
{
    public function getName(): string
    {
        return 'table';
    }

    public function configureOptions(): array
    {
        return [];
    }

    public function enabledByDefault(): bool
    {
        return false;
    }

    public function finish(
        Context $context,
        Output $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $groupedRules = [];
        $reportUncovered = $outputFormatterInput->getOptionAsBoolean(AnalyzeCommand::OPTION_REPORT_UNCOVERED);
        $reportSkipped = $outputFormatterInput->getOptionAsBoolean(AnalyzeCommand::OPTION_REPORT_SKIPPED);

        foreach ($context->rules() as $rule) {
            if ($rule instanceof Allowed) {
                continue;
            }

            if ($rule instanceof Violation || ($reportSkipped && $rule instanceof SkippedViolation)) {
                $groupedRules[$rule->getLayerA()][] = $rule;
            } elseif ($reportUncovered && $rule instanceof Uncovered) {
                $groupedRules[$rule->getLayer()][] = $rule;
            }
        }

        $style = $output->getStyle();

        foreach ($groupedRules as $layer => $rules) {
            $rows = [];
            foreach ($rules as $rule) {
                if ($rule instanceof Uncovered) {
                    $rows[] = $this->uncoveredRow($rule);
                } else {
                    $rows[] = $this->violationRow($rule);
                }
            }

            $style->table(['Reason', $layer], $rows);
        }

        if ($context->hasErrors()) {
            $this->printErrors($context, $output);
        }

        $this->printSummary($context, $output);
    }

    /**
     * @param Violation|SkippedViolation $rule
     */
    private function violationRow(Rule $rule): array
    {
        $dependency = $rule->getDependency();

        $message = sprintf(
            '<info>%s</info> must not depend on <info>%s</info> (%s)',
            $dependency->getClassLikeNameA()->toString(),
            $dependency->getClassLikeNameB()->toString(),
            $rule->getLayerB()
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
            $dependency->getOriginalDependency()->getClassLikeNameB()->toString(),
            $dependency->getOriginalDependency()->getFileOccurrence()->getLine()
        );

        return implode(" -> \n", $buffer);
    }

    private function printSummary(Context $context, Output $output): void
    {
        $violationCount = \count($context->violations());
        $skippedViolationCount = \count($context->skippedViolations());
        $uncoveredCount = \count($context->uncovered());
        $allowedCount = \count($context->allowed());

        $style = $output->getStyle();
        $style->newLine();
        $style->definitionList(
            'Report',
            new TableSeparator(),
            ['Violations' => sprintf('<fg=%s>%d</>', $violationCount > 0 ? 'red' : 'default', $violationCount)],
            ['Skipped violations' => sprintf('<fg=%s>%d</>', $skippedViolationCount > 0 ? 'yellow' : 'default', $skippedViolationCount)],
            ['Uncovered' => sprintf('<fg=%s>%d</>', $uncoveredCount > 0 ? 'yellow' : 'default', $uncoveredCount)],
            ['Allowed' => $allowedCount]
        );
    }

    private function uncoveredRow(Uncovered $rule): array
    {
        $dependency = $rule->getDependency();

        $message = sprintf(
            '<info>%s</info> has uncovered dependency on <info>%s</info>',
            $dependency->getClassLikeNameA()->toString(),
            $dependency->getClassLikeNameB()->toString()
        );

        if ($dependency instanceof InheritDependency) {
            $message .= "\n".$this->formatInheritPath($dependency);
        }

        $fileOccurrence = $rule->getDependency()->getFileOccurrence();
        $message .= sprintf("\n%s:%d", $fileOccurrence->getFilepath(), $fileOccurrence->getLine());

        return [
            '<fg=yellow>Uncovered</>',
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
}
