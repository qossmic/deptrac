<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use LogicException;
use Qossmic\Deptrac\Analyser;
use Qossmic\Deptrac\Configuration\LoaderResolver;
use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\Exception\Console\AnalyseException;
use Qossmic\Deptrac\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\OutputFormatterFactory;
use Throwable;
use function implode;
use function sprintf;

/**
 * @internal Should only be used by AnalyseCommand
 */
final class AnalyseRunner
{
    private Analyser $analyser;
    private LoaderResolver $configurationLoader;
    private OutputFormatterFactory $formatterFactory;

    public function __construct(
        Analyser $analyser,
        LoaderResolver $configurationLoader,
        OutputFormatterFactory $formatterFactory
    ) {
        $this->analyser = $analyser;
        $this->configurationLoader = $configurationLoader;
        $this->formatterFactory = $formatterFactory;
    }

    public function run(AnalyseOptions $options, Output $output): void
    {
        $configuration = $this->configurationLoader->resolve($options->getConfigurationFile())->load($options->getConfigurationFile());

        try {
            $formatter = $this->formatterFactory->getFormatterByName($options->getFormatter());
        } catch (LogicException $exception) {
            $this->printFormatterNotFoundException($output, $options->getFormatter());

            throw AnalyseException::invalidFormatter();
        }

        $formatterInput = new OutputFormatterInput(
            $options->getOutput(),
            $options->reportSkipped(),
            $options->reportUncovered(),
            $options->failOnUncovered(),
            $configuration->getFormatterConfig($formatter::getConfigName())
        );

        $this->printCollectViolations($output);
        $context = $this->analyser->analyse($configuration);

        $this->printFormattingStart($output);

        try {
            $formatter->finish($context, $output, $formatterInput);
        } catch (Throwable $error) {
            $this->printFormatterError($output, $formatter::getName(), $error);
        }

        if ($options->failOnUncovered() && $context->hasUncovered()) {
            throw AnalyseException::finishedWithUncovered();
        }
        if ($context->hasViolations()) {
            throw AnalyseException::finishedWithViolations();
        }
        if ($context->hasErrors()) {
            throw AnalyseException::failedWithErrors();
        }
    }

    private function printCollectViolations(Output $output): void
    {
        if ($output->isVerbose()) {
            $output->writeLineFormatted('<info>collecting violations.</info>');
        }
    }

    private function printFormattingStart(Output $output): void
    {
        if ($output->isVerbose()) {
            $output->writeLineFormatted('<info>formatting dependencies.</info>');
        }
    }

    private function printFormatterError(Output $output, string $formatterName, Throwable $error): void
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

    private function printFormatterNotFoundException(Output $output, string $formatterName): void
    {
        $output->writeLineFormatted('');
        $output->getStyle()->error([
            '',
            sprintf('Output formatter %s not found.', $formatterName),
            sprintf('Available formatters: ["%s"]', implode('", "', $this->formatterFactory->getFormatterNames())),
            '',
        ]);
        $output->writeLineFormatted('');
    }
}
