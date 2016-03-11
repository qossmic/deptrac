<?php


namespace SensioLabs\Deptrac\Command;

use SensioLabs\Deptrac\ConfigurationLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('init');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $command = $this->getApplication()->find('self-check');
        if ($command->run(new ArrayInput([]), $output) != 0) {
            return 1;
        }

        $configurationLoader = new ConfigurationLoader('depfile.yml');

        if ($configurationLoader->hasConfiguration()) {
            $output->writeln('<error>depfile.yml already exists</error>');

            return 1;
        }

        $configurationLoader->dumpConfiguration();
        $output->writeln('depfile <info>dumped.</info>');
    }
}
