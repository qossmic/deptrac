<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Exception;
use Qossmic\Deptrac\Analyser;
use Qossmic\Deptrac\Configuration\Loader as ConfigurationLoader;
use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Exception\Console\InvalidArgumentException;
use Qossmic\Deptrac\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\OutputFormatterFactory;
use Qossmic\Deptrac\Subscriber\ConsoleSubscriber;
use Qossmic\Deptrac\Subscriber\ProgressSubscriber;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AnalyseCommand extends Command
{
    use DefaultDepFileTrait;

    public const OPTION_REPORT_UNCOVERED = 'report-uncovered';
    public const OPTION_FAIL_ON_UNCOVERED = 'fail-on-uncovered';
    public const OPTION_REPORT_SKIPPED = 'report-skipped';

    private Analyser $analyser;
    private ConfigurationLoader $configurationLoader;
    private EventDispatcherInterface $dispatcher;
    private OutputFormatterFactory $formatterFactory;

    public function __construct(
        Analyser $analyser,
        ConfigurationLoader $configurationLoader,
        EventDispatcherInterface $dispatcher,
        OutputFormatterFactory $formatterFactory
    ) {
        $this->analyser = $analyser;
        $this->configurationLoader = $configurationLoader;
        $this->dispatcher = $dispatcher;
        $this->formatterFactory = $formatterFactory;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('analyse');
        $this->setAliases(['analyze']);

        $this->addArgument('depfile', InputArgument::OPTIONAL, 'Path to the depfile');
        $this->addOption(
            'formatter',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            sprintf(
                'Format in which to print the result of the analysis. Possible: ["%s"]',
                implode('", "', $this->formatterFactory->getFormatterNames())
            )
        );
        $this->addOption('no-progress', null, InputOption::VALUE_NONE, 'Do not show progress bar');
        $this->addOption('fail-on-uncovered', null, InputOption::VALUE_NONE, 'Fails if any uncovered dependency is found');
        $this->addOption(self::OPTION_REPORT_UNCOVERED, null, InputOption::VALUE_NONE, 'Report uncovered dependencies');
        $this->addOption(self::OPTION_REPORT_SKIPPED, null, InputOption::VALUE_NONE, 'Report skipped violations');
        $this->getDefinition()->addOptions($this->formatterFactory->getFormatterOptions());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '-1');

        $symfonyOutput = new SymfonyOutput($output, new Style(new SymfonyStyle($input, $output)));

        $file = $input->getArgument('depfile') ?? $this->getDefaultFile($symfonyOutput);

        if (!is_string($file)) {
            throw InvalidArgumentException::invalidDepfileType($file);
        }

        $configuration = $this->configurationLoader->load($file);

        $this->dispatcher->addSubscriber(new ConsoleSubscriber($output));

        if (!$input->getOption('no-progress')) {
            $this->dispatcher->addSubscriber(new ProgressSubscriber($symfonyOutput));
        }

        $this->printCollectViolations($symfonyOutput);
        $context = $this->analyser->analyse($configuration);

        $this->printFormattingStart($symfonyOutput);

        /** @var string[] $formats */
        $formats = (array) $input->getOption('formatter');

        $formatters = [];
        if ($formats) {
            $formatters = $this->formatterFactory->getFormattersByNames($formats);
        }

        if ([] === $formatters) {
            $formatters = $this->formatterFactory->getFormattersEnabledByDefault();
        }

        foreach ($formatters as $formatter) {
            try {
                $formatterConfig = $configuration->getFormatterConfig($formatter->getName());
                $formatter->finish($context, $symfonyOutput, new OutputFormatterInput(
                    $input->getOptions(), $formatterConfig
                ));
            } catch (Exception $ex) {
                $this->printFormatterException($symfonyOutput, $formatter->getName(), $ex);
            }
        }

        if ($input->getOption(self::OPTION_FAIL_ON_UNCOVERED) && $context->hasUncovered()) {
            return 1;
        }

        return $context->hasViolations() || $context->hasErrors() ? 1 : 0;
    }

    protected function printCollectViolations(SymfonyOutput $output): void
    {
        if ($output->isVerbose()) {
            $output->writeLineFormatted('<info>collecting violations.</info>');
        }
    }

    protected function printFormattingStart(SymfonyOutput $output): void
    {
        if ($output->isVerbose()) {
            $output->writeLineFormatted('<info>formatting dependencies.</info>');
        }
    }

    protected function printFormatterException(SymfonyOutput $output, string $formatterName, Exception $exception): void
    {
        $output->writeLineFormatted('');
        $output->getStyle()->error([
            '',
            sprintf('Output formatter %s threw an Exception:', $formatterName),
            sprintf('Message: %s', $exception->getMessage()),
            '',
        ]);
        $output->writeLineFormatted('');
    }
}
