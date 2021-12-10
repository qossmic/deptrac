<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DebugUnassignedCommand extends Command
{
    protected static $defaultName = 'debug:unassigned';
    protected static $defaultDescription = 'Lists all classes from your paths that are not assigned to any layer.';

    private DebugUnassignedRunner $runner;

    public function __construct(DebugUnassignedRunner $runner)
    {
        parent::__construct();

        $this->runner = $runner;
    }

    protected function configure(): void
    {
        $this->addArgument('depfile', InputArgument::REQUIRED, 'Path to the depfile');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $options = new DebugUnassignedOptions($input->getArgument('depfile'));
        $output = new SymfonyOutput($output, new Style(new SymfonyStyle($input, $output)));

        $this->runner->run($options, $output);

        return 0;
    }
}
