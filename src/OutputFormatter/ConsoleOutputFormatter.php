<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\Dependency\InheritDependency;
use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\Rule;
use SensioLabs\Deptrac\RulesetEngine\SkippedViolation;
use SensioLabs\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleOutputFormatter implements OutputFormatterInterface
{
    public function getName(): string
    {
        return 'console';
    }

    public function configureOptions(): array
    {
        return [];
    }

    public function enabledByDefault(): bool
    {
        return true;
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

            if ($rule->getDependency() instanceof InheritDependency) {
                $this->handleInheritDependency($rule, $output);
                continue;
            }

            $this->handleDependency($rule, $output);
        }

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

    /**
     * @param Violation|SkippedViolation $rule
     */
    private function handleInheritDependency(Rule $rule, OutputInterface $output): void
    {
        /** @var InheritDependency $dependency */
        $dependency = $rule->getDependency();

        $output->writeln(
            sprintf(
                "%s<info>%s</info> must not depend on <info>%s</info> (%s on %s) \n%s",
                $rule instanceof SkippedViolation ? '[SKIPPED] ' : '',
                $dependency->getClassA(),
                $dependency->getClassB(),
                $rule->getLayerA(),
                $rule->getLayerB(),
                $this->formatPath($dependency->getInheritPath(), $dependency)
            )
        );
    }

    /**
     * @param Violation|SkippedViolation $rule
     */
    private function handleDependency(Rule $rule, OutputInterface $output): void
    {
        $dependency = $rule->getDependency();

        $output->writeln(
            sprintf(
                '%s<info>%s</info>::%s must not depend on <info>%s</info> (%s on %s)',
                $rule instanceof SkippedViolation ? '[SKIPPED] ' : '',
                $dependency->getClassA(),
                $dependency->getFileOccurrence()->getLine(),
                $dependency->getClassB(),
                $rule->getLayerA(),
                $rule->getLayerB()
            )
        );
    }

    private function formatPath(AstInherit $astInherit, InheritDependency $dependency): string
    {
        $buffer = [];
        foreach ($astInherit->getPath() as $p) {
            array_unshift($buffer, sprintf("\t%s::%d", $p->getClassName(), $p->getFileOccurrence()->getLine()));
        }

        $buffer[] = sprintf("\t%s::%d", $astInherit->getClassName(), $astInherit->getFileOccurrence()->getLine());
        $buffer[] = sprintf(
            "\t%s::%d",
            $dependency->getOriginalDependency()->getClassB(),
            $dependency->getOriginalDependency()->getFileOccurrence()->getLine()
        );

        return implode(" -> \n", $buffer);
    }
}
