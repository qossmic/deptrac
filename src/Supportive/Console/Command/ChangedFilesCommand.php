<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Supportive\Console\Command;

use Qossmic\Deptrac\Supportive\Console\Symfony\Style;
use Qossmic\Deptrac\Supportive\Console\Symfony\SymfonyOutput;
use DEPTRAC_202403\Symfony\Component\Console\Command\Command;
use DEPTRAC_202403\Symfony\Component\Console\Input\InputArgument;
use DEPTRAC_202403\Symfony\Component\Console\Input\InputInterface;
use DEPTRAC_202403\Symfony\Component\Console\Input\InputOption;
use DEPTRAC_202403\Symfony\Component\Console\Output\OutputInterface;
use DEPTRAC_202403\Symfony\Component\Console\Style\SymfonyStyle;
class ChangedFilesCommand extends Command
{
    public static $defaultName = 'changed-files';
    public static $defaultDescription = 'Lists layers corresponding to the changed files';
    public function __construct(private readonly \Qossmic\Deptrac\Supportive\Console\Command\ChangedFilesRunner $runner)
    {
        parent::__construct();
    }
    protected function configure() : void
    {
        parent::configure();
        $this->addOption('with-dependencies', null, InputOption::VALUE_NONE, 'show dependent layers');
        $this->addArgument('files', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'List of changed files');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        \ini_set('memory_limit', '-1');
        $symfonyOutput = new SymfonyOutput($output, new Style(new SymfonyStyle($input, $output)));
        try {
            /** @var list<string> $files */
            $files = $input->getArgument('files');
            $this->runner->run($files, (bool) $input->getOption('with-dependencies'), $symfonyOutput);
        } catch (\Qossmic\Deptrac\Supportive\Console\Command\CommandRunException) {
            return self::FAILURE;
        }
        return self::SUCCESS;
    }
}
