<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Console\Command;

use Qossmic\Deptrac\Supportive\Console\Exception\InvalidLayerException;
use Qossmic\Deptrac\Supportive\Console\Symfony\Style;
use Qossmic\Deptrac\Supportive\Console\Symfony\SymfonyOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DebugLayerCommand extends Command
{
    public static $defaultName = 'debug:layer';
    public static $defaultDescription = 'Checks which tokens belong to the provided layer';

    private DebugLayerRunner $runner;

    public function __construct(DebugLayerRunner $runner)
    {
        $this->runner = $runner;

        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument('layer', InputArgument::OPTIONAL, 'Layer to debug');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputStyle = new Style(new SymfonyStyle($input, $output));
        $symfonyOutput = new SymfonyOutput($output, $outputStyle);

        /** @var ?string $layer */
        $layer = $input->getArgument('layer');

        try {
            $this->runner->run($layer, $symfonyOutput);
        } catch (InvalidLayerException $invalidLayerException) {
            $outputStyle->error('Layer not found.');

            return 1;
        }

        return 0;
    }
}
