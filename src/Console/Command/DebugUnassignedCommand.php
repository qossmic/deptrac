<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DebugUnassignedCommand extends Command
{
    public static $defaultName = 'debug:unassigned';
    public static $defaultDescription = 'Lists tokens that are not assigned to any layer';

    private DebugUnassignedRunner $runner;

    public function __construct(DebugUnassignedRunner $runner)
    {
        $this->runner = $runner;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output = new SymfonyOutput($output, new Style(new SymfonyStyle($input, $output)));

        $this->runner->run($output);

        return 0;
    }
}
