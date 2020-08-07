<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\Dependency\InheritDependency;
use SensioLabs\Deptrac\Env;
use SensioLabs\Deptrac\RulesetEngine\Allowed;
use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\Rule;
use SensioLabs\Deptrac\RulesetEngine\SkippedViolation;
use SensioLabs\Deptrac\RulesetEngine\Uncovered;
use SensioLabs\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

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
        OutputInterface $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $groupedRules = [];
        $maxColumnWidth = 0;
        foreach ($context->all() as $rule) {
            if ($rule instanceof Allowed) {
                continue;
            }

            $filepath = $rule->getDependency()->getFileOccurrence()->getFilepath();
            $maxColumnWidth = max($maxColumnWidth, strlen($filepath));
            $groupedRules[$filepath][] = $rule;
        }

        $reportUncovered = true === $outputFormatterInput->getOptionAsBoolean(self::REPORT_UNCOVERED);

        foreach ($groupedRules as $filepath => $rules) {
            $table = new Table($output);
            $table->setHeaders(['Line', 'Reason', $filepath]);
            $table->setColumnMaxWidth(2, $maxColumnWidth);

            foreach ($rules as $rule) {
                if ($rule instanceof Violation || $rule instanceof SkippedViolation) {
                    $this->printViolation($rule, $table);
                } elseif ($rule instanceof Uncovered && $reportUncovered) {
                    $this->printUncovered($rule, $table);
                }
            }

            $table->render();
        }

        $this->printSummary($context, $output);
    }

    /**
     * @param Violation|SkippedViolation $rule
     */
    private function printViolation(Rule $rule, Table $table): void
    {
        $dependency = $rule->getDependency();

        $table->addRow([
            $dependency->getFileOccurrence()->getLine(),
            $rule instanceof SkippedViolation ? '<warning>Skipped</warning>' : '<error>Violation</error>',
            sprintf(
                '%s<info>%s</info> must not depend on <info>%s</info> (%s on %s)',
                $rule instanceof SkippedViolation ? '[SKIPPED] ' : '',
                $dependency->getClassLikeNameA()->toString(),
                $dependency->getClassLikeNameB()->toString(),
                $rule->getLayerA(),
                $rule->getLayerB()
            ),
        ]);

        if ($dependency instanceof InheritDependency) {
            $this->printInheritPath($table, $dependency);
        }
    }

    private function printInheritPath(Table $table, InheritDependency $dependency): void
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

        $table->addRow(['', '', implode(" -> \n", $buffer)]);
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

    private function printUncovered(Uncovered $rule, Table $table): void
    {
        $dependency = $rule->getDependency();

        $table->addRow([
            $dependency->getFileOccurrence()->getLine(),
            '<comment>Uncovered</comment>',
            sprintf(
                '<info>%s</info> has uncovered dependency on <info>%s</info> (%s)',
                $dependency->getClassLikeNameA()->toString(),
                $dependency->getClassLikeNameB()->toString(),
                $rule->getLayer()
            ),
        ]);

        if ($dependency instanceof InheritDependency) {
            $this->printInheritPath($table, $dependency);
        }
    }
}
