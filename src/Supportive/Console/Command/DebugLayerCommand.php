<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Supportive\Console\Command;

use Qossmic\Deptrac\Supportive\Console\Symfony\Style;
use Qossmic\Deptrac\Supportive\Console\Symfony\SymfonyOutput;
use DEPTRAC_202401\Symfony\Component\Console\Command\Command;
use DEPTRAC_202401\Symfony\Component\Console\Input\InputArgument;
use DEPTRAC_202401\Symfony\Component\Console\Input\InputInterface;
use DEPTRAC_202401\Symfony\Component\Console\Output\OutputInterface;
use DEPTRAC_202401\Symfony\Component\Console\Style\SymfonyStyle;
class DebugLayerCommand extends Command
{
    public static $defaultName = 'debug:layer';
    public static $defaultDescription = 'Checks which tokens belong to the provided layer';
    public function __construct(private readonly \Qossmic\Deptrac\Supportive\Console\Command\DebugLayerRunner $runner)
    {
        parent::__construct();
    }
    protected function configure() : void
    {
        parent::configure();
        $this->addArgument('layer', InputArgument::OPTIONAL, 'Layer to debug');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $outputStyle = new Style(new SymfonyStyle($input, $output));
        $symfonyOutput = new SymfonyOutput($output, $outputStyle);
        /** @var ?string $layer */
        $layer = $input->getArgument('layer');
        try {
            $this->runner->run($layer, $symfonyOutput);
        } catch (\Qossmic\Deptrac\Supportive\Console\Command\CommandRunException $exception) {
            $outputStyle->error($exception->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }
}
