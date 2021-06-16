<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\ClassLikeAnalyser;
use Qossmic\Deptrac\Configuration\Loader;
use Qossmic\Deptrac\Console\Command\Exception\SingleDepfileIsRequiredException;
use Qossmic\Deptrac\Console\Symfony\Style;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DebugClassLikeCommand extends Command
{
    protected static $defaultName = 'debug:class-like';

    /** @var ClassLikeAnalyser */
    private $analyser;
    /** @var Loader */
    private $loader;

    public function __construct(
        ClassLikeAnalyser $analyser,
        Loader $loader
    ) {
        parent::__construct();

        $this->analyser = $analyser;
        $this->loader = $loader;
    }

    protected function configure(): void
    {
        $this->addArgument('depfile', InputArgument::REQUIRED, 'Path to the depfile');
        $this->addArgument('class-like', InputArgument::REQUIRED, 'Full qualified class-like name to debug');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $depfile = $input->getArgument('depfile');

        if (!is_string($depfile)) {
            throw SingleDepfileIsRequiredException::fromArgument($depfile);
        }

        /** @var string $classLike */
        $classLike = $input->getArgument('class-like');

        $configuration = $this->loader->load($depfile);

        $matchedClassLikeNames = $this->analyser->analyse($configuration, ClassLikeName::fromFQCN($classLike));

        natcasesort($matchedClassLikeNames);

        $output->writeln($matchedClassLikeNames);

        return 0;
    }
}
