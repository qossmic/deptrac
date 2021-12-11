<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use function count;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\Dependency\InheritDependency;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\Rule;
use Qossmic\Deptrac\RulesetEngine\SkippedViolation;
use Qossmic\Deptrac\RulesetEngine\Violation;

final class ConsoleOutputFormatter implements OutputFormatterInterface
{
    public static function getName(): string
    {
        return 'console';
    }

    public function finish(
        Context $context,
        Output $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $reportSkipped = $outputFormatterInput->getReportSkipped();

        foreach ($context->rules() as $rule) {
            if (!$rule instanceof Violation && !$rule instanceof SkippedViolation) {
                continue;
            }

            if (!$reportSkipped && $rule instanceof SkippedViolation) {
                continue;
            }

            $this->printViolation($rule, $output);
        }

        if ($outputFormatterInput->getReportUncovered()) {
            $this->printUncovered($context, $output);
        }

        if ($context->hasErrors()) {
            $this->printErrors($context, $output);
        }

        if ($context->hasWarnings()) {
            $this->printWarnings($context, $output);
        }

        $this->printSummary($context, $output);
    }

    /**
     * @param Violation|SkippedViolation $rule
     */
    private function printViolation(Rule $rule, Output $output): void
    {
        $dependency = $rule->getDependency();

        $output->writeLineFormatted(
            sprintf(
                '%s<info>%s</info> must not depend on <info>%s</info> (%s on %s)',
                $rule instanceof SkippedViolation ? '[SKIPPED] ' : '',
                $dependency->getDependant()->toString(),
                $dependency->getDependee()->toString(),
                $rule->getDependantLayerName(),
                $rule->getDependeeLayerName()
            )
        );
        $this->printFileOccurrence($output, $dependency->getFileOccurrence());

        if ($dependency instanceof InheritDependency) {
            $this->printInheritPath($output, $dependency);
        }
    }

    private function printInheritPath(Output $output, InheritDependency $dependency): void
    {
        $buffer = [];
        $astInherit = $dependency->getInheritPath();
        foreach ($astInherit->getPath() as $p) {
            array_unshift($buffer, sprintf("\t%s::%d", $p->getClassLikeName()->toString(), $p->getFileOccurrence()->getLine()));
        }

        $buffer[] = sprintf("\t%s::%d", $astInherit->getClassLikeName()->toString(), $astInherit->getFileOccurrence()->getLine());
        $buffer[] = sprintf(
            "\t%s::%d",
            $dependency->getOriginalDependency()->getDependee()->toString(),
            $dependency->getOriginalDependency()->getFileOccurrence()->getLine()
        );

        $output->writeLineFormatted(implode(" -> \n", $buffer));
    }

    private function printSummary(Context $context, Output $output): void
    {
        $violationCount = count($context->violations());
        $skippedViolationCount = count($context->skippedViolations());
        $uncoveredCount = count($context->uncovered());
        $allowedCount = count($context->allowed());
        $warningsCount = count($context->warnings());
        $errorsCount = count($context->errors());

        $output->writeLineFormatted('');
        $output->writeLineFormatted('Report:');
        $output->writeLineFormatted(
            sprintf(
                '<fg=%s>Violations: %d</>',
                $violationCount > 0 ? 'red' : 'default',
                $violationCount
            )
        );
        $output->writeLineFormatted(
            sprintf(
                '<fg=%s>Skipped violations: %d</>',
                $skippedViolationCount > 0 ? 'yellow' : 'default',
                $skippedViolationCount
            )
        );
        $output->writeLineFormatted(
            sprintf(
                '<fg=%s>Uncovered: %d</>',
                $uncoveredCount > 0 ? 'yellow' : 'default',
                $uncoveredCount
            )
        );
        $output->writeLineFormatted(sprintf('<info>Allowed: %d</info>', $allowedCount));
        $output->writeLineFormatted(
            sprintf(
                '<fg=%s>Warnings: %d</>',
                $warningsCount > 0 ? 'yellow' : 'default',
                $warningsCount
            )
        );
        $output->writeLineFormatted(
            sprintf(
                '<fg=%s>Errors: %d</>',
                $errorsCount > 0 ? 'red' : 'default',
                $errorsCount
            )
        );
    }

    private function printUncovered(Context $context, Output $output): void
    {
        $uncovered = $context->uncovered();
        if ([] === $uncovered) {
            return;
        }

        $output->writeLineFormatted('<comment>Uncovered dependencies:</comment>');
        foreach ($uncovered as $u) {
            $dependency = $u->getDependency();
            $output->writeLineFormatted(
                sprintf(
                    '<info>%s</info> has uncovered dependency on <info>%s</info> (%s)',
                    $dependency->getDependant()->toString(),
                    $dependency->getDependee()->toString(),
                    $u->getLayer()
                )
            );
            $this->printFileOccurrence($output, $dependency->getFileOccurrence());

            if ($dependency instanceof InheritDependency) {
                $this->printInheritPath($output, $dependency);
            }
        }
    }

    private function printFileOccurrence(Output $output, FileOccurrence $fileOccurrence): void
    {
        $output->writeLineFormatted($fileOccurrence->getFilepath().'::'.$fileOccurrence->getLine());
    }

    private function printErrors(Context $context, Output $output): void
    {
        $output->writeLineFormatted('');
        foreach ($context->errors() as $error) {
            $output->writeLineFormatted(sprintf('<fg=red>[ERROR]</> %s', $error->toString()));
        }
    }

    private function printWarnings(Context $context, Output $output): void
    {
        $output->writeLineFormatted('');
        foreach ($context->warnings() as $error) {
            $output->writeLineFormatted(sprintf('<fg=yellow>[WARNING]</> %s', $error->toString()));
        }
    }
}
