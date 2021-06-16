<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Configuration\Loader;
use Qossmic\Deptrac\Console\Command\Exception\SingleDepfileIsRequiredException;
use Qossmic\Deptrac\UnassignedAnalyser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DebugUnassignedCommand extends Command
{
    protected static $defaultName = 'debug:unassigned';
    protected static $defaultDescription = 'Lists all classes from your paths that are not assigned to any layer.';

    /** @var UnassignedAnalyser */
    private $analyser;
    /** @var Loader */
    private $loader;

    public function __construct(
        UnassignedAnalyser $analyser,
        Loader $loader
    ) {
        parent::__construct();

        $this->analyser = $analyser;
        $this->loader = $loader;
    }

    protected function configure(): void
    {
        $this->addArgument('depfile', InputArgument::REQUIRED, 'Path to the depfile');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $depfile = $input->getArgument('depfile');

        if (!is_string($depfile)) {
            throw SingleDepfileIsRequiredException::fromArgument($depfile);
        }

        $configuration = $this->loader->load($depfile);

        $output->writeln($this->analyser->analyse($configuration));

        return 0;
    }
}
