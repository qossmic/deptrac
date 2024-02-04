<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Supportive\Console\Command;

use Qossmic\Deptrac\Supportive\Console\Symfony\Style;
use Qossmic\Deptrac\Supportive\Console\Symfony\SymfonyOutput;
use DEPTRAC_202402\Symfony\Component\Console\Command\Command;
use DEPTRAC_202402\Symfony\Component\Console\Input\InputInterface;
use DEPTRAC_202402\Symfony\Component\Console\Output\OutputInterface;
use DEPTRAC_202402\Symfony\Component\Console\Style\SymfonyStyle;
class DebugUnassignedCommand extends Command
{
    public static $defaultName = 'debug:unassigned';
    public static $defaultDescription = 'Lists tokens that are not assigned to any layer';
    public function __construct(private readonly \Qossmic\Deptrac\Supportive\Console\Command\DebugUnassignedRunner $runner)
    {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $outputStyle = new Style(new SymfonyStyle($input, $output));
        $symfonyOutput = new SymfonyOutput($output, $outputStyle);
        try {
            $this->runner->run($symfonyOutput);
        } catch (\Qossmic\Deptrac\Supportive\Console\Command\CommandRunException $exception) {
            $outputStyle->error($exception->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }
}
