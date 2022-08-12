<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Console\Command;

use Psr\Container\ContainerExceptionInterface;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use Qossmic\Deptrac\Core\Analyser\LegacyDependencyLayersAnalyser;
use Qossmic\Deptrac\Supportive\Console\Exception\AnalyseException;
use Qossmic\Deptrac\Supportive\OutputFormatter\FormatterProvider;
use Throwable;
use function implode;
use function sprintf;

/**
 * @internal Should only be used by AnalyseCommand
 */
final class AnalyseRunner
{
    public function __construct(private readonly LegacyDependencyLayersAnalyser $analyser, private readonly FormatterProvider $formatterProvider)
    {
    }

    public function run(AnalyseOptions $options, OutputInterface $output): void
    {
        try {
            $formatter = $this->formatterProvider->get($options->getFormatter());
        } catch (ContainerExceptionInterface) {
            $this->printFormatterNotFoundException($output, $options->getFormatter());

            throw AnalyseException::invalidFormatter();
        }

        $formatterInput = new OutputFormatterInput(
            $options->getOutput(),
            $options->reportSkipped(),
            $options->reportUncovered(),
            $options->failOnUncovered(),
        );

        $this->printCollectViolations($output);

        $result = $this->analyser->analyse();

        $this->printFormattingStart($output);

        try {
            $formatter->finish($result, $output, $formatterInput);
        } catch (Throwable $error) {
            $this->printFormatterError($output, $formatter::getName(), $error);
        }

        if ($options->failOnUncovered() && $result->hasUncovered()) {
            throw AnalyseException::finishedWithUncovered();
        }
        if ($result->hasViolations()) {
            throw AnalyseException::finishedWithViolations();
        }
        if ($result->hasErrors()) {
            throw AnalyseException::failedWithErrors();
        }
    }

    private function printCollectViolations(OutputInterface $output): void
    {
        if ($output->isVerbose()) {
            $output->writeLineFormatted('<info>collecting violations.</info>');
        }
    }

    private function printFormattingStart(OutputInterface $output): void
    {
        if ($output->isVerbose()) {
            $output->writeLineFormatted('<info>formatting dependencies.</info>');
        }
    }

    private function printFormatterError(OutputInterface $output, string $formatterName, Throwable $error): void
    {
        $output->writeLineFormatted('');
        $output->getStyle()->error([
            '',
            sprintf('Output formatter %s threw an Exception:', $formatterName),
            sprintf('Message: %s', $error->getMessage()),
            '',
        ]);
        $output->writeLineFormatted('');
    }

    private function printFormatterNotFoundException(OutputInterface $output, string $formatterName): void
    {
        $output->writeLineFormatted('');
        $output->getStyle()->error([
            '',
            sprintf('Output formatter %s not found.', $formatterName),
            sprintf('Available formatters: ["%s"]', implode('", "', $this->formatterProvider->getKnownFormatters())),
            '',
        ]);
        $output->writeLineFormatted('');
    }
}
