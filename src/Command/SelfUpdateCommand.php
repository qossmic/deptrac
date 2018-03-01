<?php

namespace SensioLabs\Deptrac\Command;

use Humbug\SelfUpdate\Updater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Humbug\SelfUpdate\Exception\HttpRequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SelfUpdateCommand extends Command
{
    /** @var Updater */
    protected $updater;

    public function __construct(Updater $updater)
    {
        $this->updater = $updater;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setAliases(['selfupdate'])
            ->setDescription('Updates deptrac.phar to the latest version.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Updating deptrac to latest version...</info>');

        try {
            if ($this->updater->update()) {
                $output->writeln('<info>Deptrac was successfully updated.</info>');

                return 0;
            }
        } catch (HttpRequestException $e) {
            $output->writeln('<error>Could not update deptrac.</error>');
            $output->writeln('<error>'.$e->getMessage().'</error>');

            return 1;
        }

        $output->writeln('<info>Deprac is already the latest version.</info>');

        return 0;
    }
}
