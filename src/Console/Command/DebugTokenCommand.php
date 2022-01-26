<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function getcwd;
use const DIRECTORY_SEPARATOR;

class DebugTokenCommand extends Command
{
    use DefaultDepFileTrait;

    public static $defaultName = 'debug:token|debug:class-like';
    public static $defaultDescription = 'Checks which layers the provided token belongs to';

    private DebugTokenRunner $runner;

    public function __construct(DebugTokenRunner $runner)
    {
        $this->runner = $runner;

        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument('token', InputArgument::REQUIRED, 'Full qualified token name to debug');
        $this->addArgument('type', InputArgument::OPTIONAL, 'Token type (class-like, function, file)', 'class-like');
        $this->addOption(
            'depfile',
            null,
            InputOption::VALUE_REQUIRED,
            '!deprecated: use --config-file instead - Path to the depfile',
            getcwd().DIRECTORY_SEPARATOR.'depfile.yaml'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyOutput = new SymfonyOutput($output, new Style(new SymfonyStyle($input, $output)));
        /** @var string $tokenName */
        $tokenName = $input->getArgument('token');
        /** @var string $tokenType */
        $tokenType = $input->getArgument('type');
        $options = new DebugTokenOptions(
            self::getConfigFile($input, $symfonyOutput),
            $tokenName,
            $tokenType
        );

        $this->runner->run($options, $symfonyOutput);

        return 0;
    }
}
