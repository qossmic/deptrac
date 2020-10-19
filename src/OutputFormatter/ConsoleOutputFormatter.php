<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Console\Output;
use SensioLabs\Deptrac\Dependency\InheritDependency;
use SensioLabs\Deptrac\Env;
use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\Rule;
use SensioLabs\Deptrac\RulesetEngine\SkippedViolation;
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
        foreach ($context->all() as $rule) {
            if (!$rule instanceof Violation && !$rule instanceof SkippedViolation) {
                continue;
            }

            $this->printViolation($rule, $output);
        }

        if (true === $outputFormatterInput->getOptionAsBoolean(static::REPORT_UNCOVERED)) {
            $this->printUncovered($context, $output);
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
                $dependency->getClassLikeNameA()->toString(),
                $dependency->getClassLikeNameB()->toString(),
                $rule->getLayerA(),
                $rule->getLayerB()
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
            $dependency->getOriginalDependency()->getClassLikeNameB()->toString(),
            $dependency->getOriginalDependency()->getFileOccurrence()->getLine()
        );

        $output->writeLineFormatted(implode(" -> \n", $buffer));
    }

    private function printSummary(Context $context, Output $output): void
    {
        $violationCount = \count($context->violations());
        $skippedViolationCount = \count($context->skippedViolations());
        $uncoveredCount = \count($context->uncovered());
        $allowedCount = \count($context->allowed());

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
                    $dependency->getClassLikeNameA()->toString(),
                    $dependency->getClassLikeNameB()->toString(),
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
}
