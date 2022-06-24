<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Console\Env;
use Qossmic\Deptrac\Console\Exception\AnalyseException;
use Qossmic\Deptrac\Console\Subscriber\ConsoleSubscriber;
use Qossmic\Deptrac\Console\Subscriber\ProgressSubscriber;
use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\OutputFormatter\FormatterProvider;
use Qossmic\Deptrac\OutputFormatter\GithubActionsOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\TableOutputFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use function getcwd;
use const DIRECTORY_SEPARATOR;

class AnalyseCommand extends Command
{
    public const OPTION_REPORT_UNCOVERED = 'report-uncovered';
    public const OPTION_FAIL_ON_UNCOVERED = 'fail-on-uncovered';
    public const OPTION_REPORT_SKIPPED = 'report-skipped';

    public static $defaultName = 'analyse|analyze';
    public static $defaultDescription = 'Analyses your project using the provided depfile';

    private AnalyseRunner $runner;
    private EventDispatcherInterface $dispatcher;
    private FormatterProvider $formatterProvider;

    public function __construct(
        AnalyseRunner $runner,
        EventDispatcherInterface $dispatcher,
        FormatterProvider $formatterProvider
    ) {
        $this->runner = $runner;
        $this->dispatcher = $dispatcher;
        $this->formatterProvider = $formatterProvider;

        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument(
            'depfile',
            InputArgument::OPTIONAL,
            '!deprecated: use --config-file instead - Path to the depfile',
            getcwd().DIRECTORY_SEPARATOR.'depfile.yaml'
        );
        $this->addOption(
            'formatter',
            'f',
            InputOption::VALUE_OPTIONAL,
            sprintf(
                'Format in which to print the result of the analysis. Possible: ["%s"]',
                implode('", "', $this->formatterProvider->getKnownFormatters())
            )
        );
        $this->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Output file path for formatter (if applicable)');
        $this->addOption('no-progress', null, InputOption::VALUE_NONE, 'Do not show progress bar');
        $this->addOption(self::OPTION_FAIL_ON_UNCOVERED, null, InputOption::VALUE_NONE, 'Fails if any uncovered dependency is found');
        $this->addOption(self::OPTION_REPORT_UNCOVERED, null, InputOption::VALUE_NONE, 'Report uncovered dependencies');
        $this->addOption(self::OPTION_REPORT_SKIPPED, null, InputOption::VALUE_NONE, 'Report skipped violations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '-1');

        $symfonyOutput = new SymfonyOutput($output, new Style(new SymfonyStyle($input, $output)));
        /** @var ?string $formatter */
        $formatter = $input->getOption('formatter');
        $formatter = $formatter ?? self::getDefaultFormatter();

        /** @var string|numeric|null $output */
        $output = $input->getOption('output');

        $options = new AnalyseOptions(
            (bool) $input->getOption('no-progress'),
            $formatter,
            null === $output ? null : (string) $output,
            (bool) $input->getOption(self::OPTION_REPORT_SKIPPED),
            (bool) $input->getOption(self::OPTION_REPORT_UNCOVERED),
            (bool) $input->getOption(self::OPTION_FAIL_ON_UNCOVERED)
        );

        $this->dispatcher->addSubscriber(new ConsoleSubscriber($symfonyOutput));
        if ($options->showProgress()) {
            $this->dispatcher->addSubscriber(new ProgressSubscriber($symfonyOutput));
        }

        try {
            $this->runner->run($options, $symfonyOutput);
        } catch (AnalyseException $analyseException) {
            return 1;
        }

        return 0;
    }

    public static function getDefaultFormatter(): string
    {
        return false !== (new Env())->get('GITHUB_ACTIONS') ? GithubActionsOutputFormatter::getName() : TableOutputFormatter::getName();
    }
}
