<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Dependency\InheritDependency;
use SensioLabs\Deptrac\Env;
use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\Rule;
use SensioLabs\Deptrac\RulesetEngine\SkippedViolation;
use SensioLabs\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleOutputFormatter implements OutputFormatterInterface
{
    private const REPORT_UNCOVERED = 'report-uncovered';

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
        return !(false !== $this->env->get('GITHUB_ACTIONS'));
    }

    public function finish(
        Context $context,
        OutputInterface $output,
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
    private function printViolation(Rule $rule, OutputInterface $output): void
    {
        $dependency = $rule->getDependency();

        $output->writeln(
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

    private function printInheritPath(OutputInterface $output, InheritDependency $dependency): void
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

        $output->writeln(implode(" -> \n", $buffer));
    }

    private function printSummary(Context $context, OutputInterface $output): void
    {
        $violationCount = \count($context->violations());
        $skippedViolationCount = \count($context->skippedViolations());
        $uncoveredCount = \count($context->uncovered());
        $allowedCount = \count($context->allowed());

        $output->writeln('');
        $output->writeln('Report:');
        $output->writeln(
            sprintf(
                '<%1$s>Violations: %2$d</%1$s>',
                $violationCount > 0 ? 'error' : 'info',
                $violationCount
            )
        );
        $output->writeln(
            sprintf(
                '<%1$s>Skipped violations: %2$d</%1$s>',
                $skippedViolationCount > 0 ? 'comment' : 'info',
                $skippedViolationCount
            )
        );
        $output->writeln(
            sprintf(
                '<%1$s>Uncovered: %2$d</%1$s>',
                $uncoveredCount > 0 ? 'comment' : 'info',
                $uncoveredCount
            )
        );
        $output->writeln(sprintf('<info>Allowed: %d</info>', $allowedCount));
    }

    private function printUncovered(Context $context, OutputInterface $output): void
    {
        $uncovered = $context->uncovered();
        if ([] === $uncovered) {
            return;
        }

        $output->writeln('<comment>Uncovered dependencies:</comment>');
        foreach ($uncovered as $u) {
            $dependency = $u->getDependency();
            $output->writeln(
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

    private function printFileOccurrence(OutputInterface $output, FileOccurrence $fileOccurrence): void
    {
        $output->writeln($fileOccurrence->getFilepath().'::'.$fileOccurrence->getLine());
    }
}
