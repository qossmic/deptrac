<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Console\Command;

use SensioLabs\Deptrac\Analyser;
use SensioLabs\Deptrac\Configuration\Loader as ConfigurationLoader;
use SensioLabs\Deptrac\Console\Command\Exception\SingleDepfileIsRequiredException;
use SensioLabs\Deptrac\OutputFormatterFactory;
use SensioLabs\Deptrac\Subscriber\ConsoleSubscriber;
use SensioLabs\Deptrac\Subscriber\ProgressSubscriber;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AnalyzeCommand extends Command
{
    private $analyser;
    private $configurationLoader;
    private $dispatcher;
    private $formatterFactory;

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
        $this->setName('analyze');
        $this->setAliases(['analyse']);

        $this->addArgument('depfile', InputArgument::OPTIONAL, 'Path to the depfile', getcwd().'/depfile.yml');
        $this->getDefinition()->addOptions($this->formatterFactory->getFormatterOptions());
        $this->addOption('no-banner', null, InputOption::VALUE_NONE, 'Do not show banner');
        $this->addOption('no-progress', null, InputOption::VALUE_NONE, 'Do not show progress bar');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '-1');

        if (!$input->getOption('no-banner')) {
            $this->printBanner($output);
        }

        $file = $input->getArgument('depfile');
        if (!is_string($file)) {
            throw SingleDepfileIsRequiredException::fromArgument($file);
        }

        $configuration = $this->configurationLoader->load($file);

        $this->dispatcher->addSubscriber(new ConsoleSubscriber($output));

        if (!$input->getOption('no-progress')) {
            $this->dispatcher->addSubscriber(new ProgressSubscriber($output));
        }

        $this->printCollectViolations($output);
        $dependencyContext = $this->analyser->analyse($configuration);

        $this->printFormattingStart($output);

        foreach ($this->formatterFactory->getActiveFormatters($input) as $formatter) {
            try {
                $formatter->finish(
                    $dependencyContext,
                    $output,
                    $this->formatterFactory->getOutputFormatterInput($formatter, $input)
                );
            } catch (\Exception $ex) {
                $this->printFormatterException($output, $formatter->getName(), $ex);
            }
        }

        return $dependencyContext->hasViolations() ? 1 : 0;
    }

    protected function printBanner(OutputInterface $output): void
    {
        $output->writeln("\n<comment>deptrac is alpha, not production ready.\nplease help us and report feedback / bugs.</comment>\n");
    }

    protected function printCollectViolations(OutputInterface $output): void
    {
        $output->writeln('<info>collecting violations.</info>', OutputInterface::VERBOSITY_VERBOSE);
    }

    protected function printFormattingStart(OutputInterface $output): void
    {
        $output->writeln('<info>formatting dependencies.</info>', OutputInterface::VERBOSITY_VERBOSE);
    }

    protected function printFormatterException(OutputInterface $output, string $formatterName, \Exception $exception): void
    {
        $output->writeln('');
        $errorMessages = [
            '',
            sprintf('Output formatter %s threw an Exception:', $formatterName),
            sprintf('Message: %s', $exception->getMessage()),
            '',
        ];
        $output->writeln($this->getHelper('formatter')->formatBlock($errorMessages, 'error'));
        $output->writeln('');
    }
}
