<?php


namespace DependencyTracker\OutputFormatter;

use DependencyTracker\ClassNameLayerResolverInterface;
use DependencyTracker\DependencyResult;
use DependencyTracker\DependencyResult\InheritDependency;
use DependencyTracker\RulesetEngine\RulesetViolation;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstMap\AstInheritInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputFormatter implements OutputFormatterInterface
{
    public function getName()
    {
        return 'console';
    }

    /**
     * @param AstMap                          $astMap
     * @param RulesetViolation[]              $violations
     * @param DependencyResult                $dependencyResult
     * @param ClassNameLayerResolverInterface $classNameLayerResolver
     * @param OutputInterface                 $output
     */
    public function finish(
        AstMap $astMap,
        array $violations,
        DependencyResult $dependencyResult,
        ClassNameLayerResolverInterface $classNameLayerResolver,
        OutputInterface $output
    ) {
        foreach ($violations as $violation) {
            if ($violation->getDependency() instanceof InheritDependency) {
                $this->handleInheritDependency($violation, $output);
                continue;
            }

            $this->handleDependeny($violation, $output);
        }

        $output->writeln(sprintf("\nFound <error>%s Violations</error>", count($violations)));
    }

    private function handleInheritDependency(RulesetViolation $violation, OutputInterface $output)
    {
        $output->writeln(
            sprintf(
                "<info>%s</info> must not depend on <info>%s</info> (%s on %s) \n%s",
                $violation->getDependency()->getClassA(),
                $violation->getDependency()->getClassB(),
                $violation->getLayerA(),
                $violation->getLayerB(),
                $this->formatPath($violation->getDependency()->getPath(), $violation->getDependency())
            )
        );
    }

    private function handleDependeny(RulesetViolation $violation, OutputInterface $output)
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
            array_unshift($buffer, "\t".$p->getClassName().'::'.$p->getLine());
        }

        $buffer[] = "\t".$astInherit->getClassName().'::'.$astInherit->getLine();
        $buffer[] = "\t".$dependency->getOriginalDependency()->getClassB().'::'.$dependency->getOriginalDependency()->getClassALine();

        return implode(" -> \n", $buffer);
    }
}
