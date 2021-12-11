<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Configuration\Dumper as ConfigurationDumper;
use Qossmic\Deptrac\Exception\Configuration\FileAlreadyExistsException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    private ConfigurationDumper $dumper;

    public function __construct(ConfigurationDumper $dumper)
    {
        $this->dumper = $dumper;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('init');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->dumper->dump('depfile.yaml');
            $output->writeln('depfile <info>dumped.</info>');

            return 0;
        } catch (FileAlreadyExistsException $e) {
            $output->writeln('<error>depfile.yaml already exists</error>');

            return 1;
        }
    }
}
