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

class DebugTokenCommand extends Command
{
    use DefaultDepFileTrait;

    private DebugTokenRunner $runner;

    public function __construct(DebugTokenRunner $runner)
    {
        parent::__construct();

        $this->runner = $runner;
    }

    protected function configure(): void
    {
        $this->setName('debug:token');
        $this->setAliases(['debug:class-like']);

        $this->addArgument('token', InputArgument::REQUIRED, 'Full qualified token name to debug');
        $this->addArgument('type', InputArgument::OPTIONAL, 'Token type (class-like, function, file)', 'class-like');
        $this->addOption('depfile', null, InputOption::VALUE_OPTIONAL, 'Path to the depfile');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyOutput = new SymfonyOutput($output, new Style(new SymfonyStyle($input, $output)));
        /** @var string $tokenName */
        $tokenName = $input->getArgument('token');
        /** @var string $tokenType */
        $tokenType = $input->getArgument('type');
        $options = new DebugTokenOptions(
            $input->getOption('depfile') ?? $this->getDefaultFile($symfonyOutput),
            $tokenName,
            $tokenType
        );

        $this->runner->run($options, $symfonyOutput);

        return 0;
    }
}
