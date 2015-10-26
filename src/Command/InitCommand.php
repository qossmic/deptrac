<?php 

namespace DependencyTracker\Command;

use DependencyTracker\ConfigurationLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{

    protected $configurationLoader;

    public function __construct(
        ConfigurationLoader $configurationLoader
    )
    {
        parent::__construct();
        $this->configurationLoader = $configurationLoader;
    }

    protected function configure()
    {
        $this->setName('init');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {

        if ($this->configurationLoader->hasConfiguration()) {
            $output->writeln('<error>depfile already exists</error>');
            return 1;
        }

        $this->configurationLoader->dumpConfiguration();
        $output->writeln("depfile <info>dumped.</info>");
    }
}
