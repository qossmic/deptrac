<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Supportive\Console\Command;

use DEPTRAC_202401\Psr\Container\ContainerExceptionInterface;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use Qossmic\Deptrac\Contract\Result\OutputResult;
use Qossmic\Deptrac\Core\Analyser\AnalyserException;
use Qossmic\Deptrac\Core\Analyser\DependencyLayersAnalyser;
use Qossmic\Deptrac\Supportive\OutputFormatter\FormatterProvider;
use Throwable;
use function implode;
use function sprintf;
/**
 * @internal Should only be used by AnalyseCommand
 */
final class AnalyseRunner
{
    public function __construct(private readonly DependencyLayersAnalyser $analyser, private readonly FormatterProvider $formatterProvider)
    {
    }
    /**
     * @throws CommandRunException
     */
    public function run(\Qossmic\Deptrac\Supportive\Console\Command\AnalyseOptions $options, OutputInterface $output) : void
    {
        try {
            $formatter = $this->formatterProvider->get($options->formatter);
        } catch (ContainerExceptionInterface) {
            $this->printFormatterNotFoundException($output, $options->formatter);
            throw \Qossmic\Deptrac\Supportive\Console\Command\CommandRunException::invalidFormatter();
        }
        $formatterInput = new OutputFormatterInput($options->output, $options->reportSkipped, $options->reportUncovered, $options->failOnUncovered);
        $this->printCollectViolations($output);
        try {
            $result = OutputResult::fromAnalysisResult($this->analyser->analyse());
        } catch (AnalyserException $e) {
            $this->printAnalysisException($output, $e);
            throw \Qossmic\Deptrac\Supportive\Console\Command\CommandRunException::analyserException($e);
        }
        $this->printFormattingStart($output);
        try {
            $formatter->finish($result, $output, $formatterInput);
        } catch (Throwable $error) {
            $this->printFormatterError($output, $formatter::getName(), $error);
        }
        if ($options->failOnUncovered && $result->hasUncovered()) {
            throw \Qossmic\Deptrac\Supportive\Console\Command\CommandRunException::finishedWithUncovered();
        }
        if ($result->hasViolations()) {
            throw \Qossmic\Deptrac\Supportive\Console\Command\CommandRunException::finishedWithViolations();
        }
        if ($result->hasErrors()) {
            throw \Qossmic\Deptrac\Supportive\Console\Command\CommandRunException::failedWithErrors();
        }
    }
    private function printCollectViolations(OutputInterface $output) : void
    {
        if ($output->isVerbose()) {
            $output->writeLineFormatted('<info>collecting violations.</info>');
        }
    }
    private function printFormattingStart(OutputInterface $output) : void
    {
        if ($output->isVerbose()) {
            $output->writeLineFormatted('<info>formatting dependencies.</info>');
        }
    }
    private function printFormatterError(OutputInterface $output, string $formatterName, Throwable $error) : void
    {
        $output->writeLineFormatted('');
        $output->getStyle()->error(['', sprintf('Output formatter %s threw an Exception:', $formatterName), sprintf('Message: %s', $error->getMessage()), '']);
        $output->writeLineFormatted('');
    }
    private function printAnalysisException(OutputInterface $output, AnalyserException $exception) : void
    {
        $exceptionMessageStack = [$exception->getMessage()];
        $previous = $exception->getPrevious();
        while (null !== $previous) {
            $exceptionMessageStack[] = $previous->getMessage();
            $previous = $previous->getPrevious();
        }
        $message = ['Analysis finished with an Exception.', ...$exceptionMessageStack];
        $output->getStyle()->error($message);
    }
    private function printFormatterNotFoundException(OutputInterface $output, string $formatterName) : void
    {
        $output->writeLineFormatted('');
        $output->getStyle()->error(['', sprintf('Output formatter %s not found.', $formatterName), sprintf('Available formatters: ["%s"]', implode('", "', $this->formatterProvider->getKnownFormatters())), '']);
        $output->writeLineFormatted('');
    }
}
