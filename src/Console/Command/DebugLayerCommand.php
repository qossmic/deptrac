<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Exception\Console\InvalidLayerException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DebugLayerCommand extends Command
{
    use DefaultDepFileTrait;

    protected static $defaultName = 'debug:layer';

    private DebugLayerRunner $runner;

    public function __construct(DebugLayerRunner $runner)
    {
        parent::__construct();

        $this->runner = $runner;
    }

    protected function configure(): void
    {
        $this->addArgument('layer', InputArgument::OPTIONAL, 'Layer to debug');
        $this->addOption('depfile', null, InputOption::VALUE_OPTIONAL, 'Path to the depfile');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputStyle = new Style(new SymfonyStyle($input, $output));
        $symfonyOutput = new SymfonyOutput($output, $outputStyle);
        /** @var ?string $layer */
        $layer = $input->getArgument('layer');
        $options = new DebugLayerOptions(
            $input->getOption('depfile') ?? $this->getDefaultFile(new SymfonyOutput($output, $outputStyle)),
            $layer
        );

        try {
            $this->runner->run($options, $symfonyOutput);
        } catch (InvalidLayerException $invalidLayerException) {
            $outputStyle->error('Layer not found.');

            return 1;
        }

        return 0;
    }
}
