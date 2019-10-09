<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\DependencyContext;
use Symfony\Component\Console\Output\OutputInterface;

final class SarbOutputFormatter implements OutputFormatterInterface
{
    private static $argument_dump_sarb = 'dump-sarb';

    public function getName(): string
    {
        return 'sarb';
    }

    /**
     * @return OutputFormatterOption[]
     */
    public function configureOptions(): array
    {
        return [
            OutputFormatterOption::newValueOption(static::$argument_dump_sarb, 'path to a dumped sarb file', './sarb.json'),
        ];
    }

    public function enabledByDefault(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function finish(
        DependencyContext $dependencyContext,
        OutputInterface $output,
        OutputFormatterInput $outputFormatterInput
    ): void {

        $sarbIssues = [];

        foreach ($dependencyContext->getViolations() as $violation) {

            $dependency = $violation->getDependency();

            $type = sprintf(
                '%s on %s',
                $violation->getLayerA(),
                $violation->getLayerB()
            );

            $message = sprintf(
                '%s:%s must not depend on %s (%s on %s)',
                $dependency->getClassA(),
                $dependency->getClassALine(),
                $dependency->getClassB(),
                $violation->getLayerA(),
                $violation->getLayerB()
            );

            $sarbIssues[] = [
                'file' => $dependency->getFilename(),
                'line' => $dependency->getClassALine(),
                'type' => $type,
                'message' => $message,
            ];
        }


        $json = json_encode($sarbIssues);

        if ($dumpSarbPath = $outputFormatterInput->getOption(static::$argument_dump_sarb)) {
            file_put_contents($dumpSarbPath, $json);
            $output->writeln('<info>JUnit Report dumped to '.realpath($dumpSarbPath).'</info>');
        }
    }

}
