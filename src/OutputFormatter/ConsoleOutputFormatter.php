<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\DependencyResult\InheritDependency;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputFormatter implements OutputFormatterInterface
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
        DependencyContext $dependencyContext,
        OutputInterface $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        foreach ($dependencyContext->getViolations() as $violation) {
            if ($violation->getDependency() instanceof InheritDependency) {
                $this->handleInheritDependency($violation, $output, $dependencyContext->isViolationSkipped($violation));
                continue;
            }

            $this->handleDependency($violation, $output, $dependencyContext->isViolationSkipped($violation));
        }

        $violationCount = \count($dependencyContext->getViolations());
        $skippedViolationCount = \count($dependencyContext->getSkippedViolations());
        if ($violationCount > $skippedViolationCount) {
            $output->writeln(
                sprintf(
                    'Found <error>%s Violations</error>'.($skippedViolationCount ? ' and %s Violations skipped' : ''),
                    $violationCount - $skippedViolationCount,
                    $skippedViolationCount
                )
            );
        } else {
            $output->writeln(
                sprintf(
                    'Found <info>%s Violations</info>'.($skippedViolationCount ? ' and %s Violations skipped' : ''),
                    $violationCount - $skippedViolationCount,
                    $skippedViolationCount
                )
            );
        }
    }

    private function handleInheritDependency(RulesetViolation $violation, OutputInterface $output, bool $isSkipped)
    {
        /** @var InheritDependency $dependency */
        $dependency = $violation->getDependency();
        $output->writeln(
            sprintf(
                "%s<info>%s</info> must not depend on <info>%s</info> (%s on %s) \n%s",
                $isSkipped ? '[SKIPPED] ' : '',
                $dependency->getClassA(),
                $dependency->getClassB(),
                $violation->getLayerA(),
                $violation->getLayerB(),
                $this->formatPath($dependency->getPath(), $dependency)
            )
        );
    }

    private function handleDependency(RulesetViolation $violation, OutputInterface $output, bool $isSkipped)
    {
        $output->writeln(
            sprintf(
                '%s<info>%s</info>::%s must not depend on <info>%s</info> (%s on %s)',
                $isSkipped ? '[SKIPPED] ' : '',
                $violation->getDependency()->getClassA(),
                $violation->getDependency()->getClassALine(),
                $violation->getDependency()->getClassB(),
                $violation->getLayerA(),
                $violation->getLayerB()
            )
        );
    }

    private function formatPath(AstInherit $astInherit, InheritDependency $dependency)
    {
        $buffer = [];
        foreach ($astInherit->getPath() as $p) {
            array_unshift($buffer, sprintf("\t%s::%d", $p->getClassName(), $p->getLine()));
        }

        $buffer[] = sprintf("\t%s::%d", $astInherit->getClassName(), $astInherit->getLine());
        $buffer[] = sprintf(
            "\t%s::%d",
            $dependency->getOriginalDependency()->getClassB(),
            $dependency->getOriginalDependency()->getClassALine()
        );

        return implode(" -> \n", $buffer);
    }
}
