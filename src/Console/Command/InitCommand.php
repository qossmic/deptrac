<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\File\Dumper as ConfigurationDumper;
use Qossmic\Deptrac\File\Exception\FileNotWritableException;
use Qossmic\Deptrac\Finder\Exception\FileAlreadyExistsException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function sprintf;

class InitCommand extends Command
{
    public static $defaultName = 'init';
    public static $defaultDescription = 'Creates a depfile template';

    private ConfigurationDumper $dumper;

    public function __construct(ConfigurationDumper $dumper)
    {
        $this->dumper = $dumper;

        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setName('init');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            /** @var string $targetFile */
            $targetFile = $input->getOption('config-file');
            $this->dumper->dump($targetFile);
            $output->writeln('Depfile <info>dumped.</info>');

            return 0;
        } catch (FileNotWritableException|FileAlreadyExistsException $fileException) {
            $output->writeln(sprintf('<error>%s</error>', $fileException->getMessage()));

            return 1;
        }
    }
}
