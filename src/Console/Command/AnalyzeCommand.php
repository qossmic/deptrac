<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Console\Command;

use SensioLabs\Deptrac\Analyser;
use SensioLabs\Deptrac\Configuration\Loader as ConfigurationLoader;
use SensioLabs\Deptrac\Console\Command\Exception\SingleDepfileIsRequiredException;
use SensioLabs\Deptrac\Console\Symfony\SymfonyOutput;
use SensioLabs\Deptrac\Console\Symfony\Style;
use SensioLabs\Deptrac\OutputFormatterFactory;
use SensioLabs\Deptrac\Subscriber\ConsoleSubscriber;
use SensioLabs\Deptrac\Subscriber\ProgressSubscriber;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AnalyzeCommand extends Command
{
    /** @var Analyser */
    private $analyser;
    /** @var ConfigurationLoader */
    private $configurationLoader;
    /** @var EventDispatcherInterface */
    private $dispatcher;
    /** @var OutputFormatterFactory */
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

        $this->addArgument('depfile', InputArgument::OPTIONAL, 'Path to the depfile');
        $this->getDefinition()->addOptions($this->formatterFactory->getFormatterOptions());
        $this->addOption('no-progress', null, InputOption::VALUE_NONE, 'Do not show progress bar');
        $this->addOption('fail-on-uncovered', null, InputOption::VALUE_NONE, 'Fails if any uncovered dependecy is found');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '-1');

        $file = $input->getArgument('depfile');

        if (null === $file) {
            $file = $this->getDefaultFile($output);
        }

        if (!is_string($file)) {
            throw SingleDepfileIsRequiredException::fromArgument($file);
        }

        $symfonyOutput = new SymfonyOutput($output, new Style(new SymfonyStyle($input, $output)));

        $configuration = $this->configurationLoader->load($file);

        $this->dispatcher->addSubscriber(new ConsoleSubscriber($output));

        if (!$input->getOption('no-progress')) {
            $this->dispatcher->addSubscriber(new ProgressSubscriber($symfonyOutput));
        }

        $this->printCollectViolations($output);
        $context = $this->analyser->analyse($configuration);

        $this->printFormattingStart($output);

        foreach ($this->formatterFactory->getActiveFormatters($input) as $formatter) {
            try {
                $formatter->finish(
                    $context,
                    $symfonyOutput,
                    $this->formatterFactory->getOutputFormatterInput($formatter, $input)
                );
            } catch (\Exception $ex) {
                $this->printFormatterException($output, $formatter->getName(), $ex);
            }
        }

        if ($input->getOption('fail-on-uncovered') && $context->hasUncovered()) {
            return 1;
        }

        return $context->hasViolations() ? 1 : 0;
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

    protected function getDefaultFile(OutputInterface $output): string
    {
        $oldDefaultFile = getcwd().'/depfile.yml';

        if (is_file($oldDefaultFile)) {
            $errorMessages = [
                '',
                'Old default file detected.',
                'The default file changed from depfile.yml to depfile.yaml.',
                'You are getting this message because you are using deptrac without the file argument and the old default file.',
                'Deptrac loads for now the old file. Please update your file extension from yml to yaml.',
                '',
            ];
            $output->writeln($this->getHelper('formatter')->formatBlock($errorMessages, 'comment'));

            return $oldDefaultFile;
        }

        return getcwd().'/depfile.yaml';
    }
}
