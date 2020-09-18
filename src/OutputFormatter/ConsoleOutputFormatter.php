<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\Console\Output;
use SensioLabs\Deptrac\Dependency\InheritDependency;
use SensioLabs\Deptrac\Env;
use SensioLabs\Deptrac\RulesetEngine\Allowed;
use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\Rule;
use SensioLabs\Deptrac\RulesetEngine\SkippedViolation;
use SensioLabs\Deptrac\RulesetEngine\Uncovered;
use SensioLabs\Deptrac\RulesetEngine\Violation;

final class ConsoleOutputFormatter implements OutputFormatterInterface
{
    private const REPORT_UNCOVERED = 'report-uncovered';

    /** @var Env */
    private $env;

    public function __construct(Env $env = null)
    {
        $this->env = $env ?? new Env();
    }

    public function getName(): string
    {
        return 'console';
    }

    public function configureOptions(): array
    {
        return [
            OutputFormatterOption::newValueOption(static::REPORT_UNCOVERED, 'report uncovered dependencies', false),
        ];
    }

    public function enabledByDefault(): bool
    {
        return false === $this->env->get('GITHUB_ACTIONS');
    }

    public function finish(
        Context $context,
        Output $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $groupedRules = [];
        $reportUncovered = true === $outputFormatterInput->getOptionAsBoolean(self::REPORT_UNCOVERED);

        foreach ($context->all() as $rule) {
            if ($rule instanceof Allowed) {
                continue;
            }

            if ($rule instanceof Violation || $rule instanceof SkippedViolation) {
                $groupedRules[$rule->getLayerA()][] = $rule;
            } elseif ($reportUncovered && $rule instanceof Uncovered) {
                $groupedRules[$rule->getLayer()][] = $rule;
            }
        }

        $style = $output->getStyle();

        foreach ($groupedRules as $layer => $rules) {
            $rows = [];
            foreach ($rules as $rule) {
                if ($rule instanceof Violation || $rule instanceof SkippedViolation) {
                    $rows[] = $this->violationRow($rule);
                } elseif ($reportUncovered && $rule instanceof Uncovered) {
                    $rows[] = $this->uncoveredRow($rule);
                }
            }

            $style->table(['Reason', $layer], $rows);
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
            '%s<info>%s</info> must not depend on <info>%s</info> (%s)',
            $rule instanceof SkippedViolation ? '[SKIPPED] ' : '',
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
            $rule instanceof SkippedViolation ? '<warning>Skipped</warning>' : '<error>Violation</error>',
            $message
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
        $output->writeLineFormatted('Report:');
        $output->writeLineFormatted(
            sprintf(
                '<%1$s>Violations: %2$d</%1$s>',
                $violationCount > 0 ? 'error' : 'info',
                $violationCount
            )
        );
        $output->writeLineFormatted(
            sprintf(
                '<%1$s>Skipped violations: %2$d</%1$s>',
                $skippedViolationCount > 0 ? 'comment' : 'info',
                $skippedViolationCount
            )
        );
        $output->writeLineFormatted(
            sprintf(
                '<%1$s>Uncovered: %2$d</%1$s>',
                $uncoveredCount > 0 ? 'comment' : 'info',
                $uncoveredCount
            )
        );
        $output->writeLineFormatted(sprintf('<info>Allowed: %d</info>', $allowedCount));
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
            '<comment>Uncovered</comment>',
            $message
        ];
    }
}
