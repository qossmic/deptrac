<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Console\Command;

use Qossmic\Deptrac\Supportive\Console\Symfony\Style;
use Qossmic\Deptrac\Supportive\Console\Symfony\SymfonyOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DebugUnassignedCommand extends Command
{
    public static $defaultName = 'debug:unassigned';
    public static $defaultDescription = 'Lists tokens that are not assigned to any layer';
    public const EXIT_WITH_UNASSIGNED_TOKENS = 2;

    public function __construct(private readonly DebugUnassignedRunner $runner)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputStyle = new Style(new SymfonyStyle($input, $output));
        $symfonyOutput = new SymfonyOutput($output, $outputStyle);

        try {
            $result = $this->runner->run($symfonyOutput);

            return $result ? self::EXIT_WITH_UNASSIGNED_TOKENS : self::SUCCESS;
        } catch (CommandRunException $exception) {
            $outputStyle->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
