<?php

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\AstRunner\AstMap\AstInheritInterface;
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
                $this->handleInheritDependency($violation, $output);
                continue;
            }

            $this->handleDependency($violation, $output);
        }

        if (count($dependencyContext->getViolations())) {
            $output->writeln(sprintf("\nFound <error>%s Violations</error>", count($dependencyContext->getViolations())));
        } else {
            $output->writeln(sprintf("\nFound <info>%s Violations</info>", count($dependencyContext->getViolations())));
        }
    }

    private function handleInheritDependency(RulesetViolation $violation, OutputInterface $output)
    {
        /** @var InheritDependency $dependency */
        $dependency = $violation->getDependency();
        $output->writeln(
            sprintf(
                "<info>%s</info> must not depend on <info>%s</info> (%s on %s) \n%s",
                $dependency->getClassA(),
                $dependency->getClassB(),
                $violation->getLayerA(),
                $violation->getLayerB(),
                $this->formatPath($dependency->getPath(), $dependency)
            )
        );
    }

    private function handleDependency(RulesetViolation $violation, OutputInterface $output)
    {
        $output->writeln(
            sprintf(
                '<info>%s</info>::%s must not depend on <info>%s</info> (%s on %s)',
                $violation->getDependency()->getClassA(),
                $violation->getDependency()->getClassALine(),
                $violation->getDependency()->getClassB(),
                $violation->getLayerA(),
                $violation->getLayerB()
            )
        );
    }

    private function formatPath(AstInheritInterface $astInherit, InheritDependency $dependency)
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
