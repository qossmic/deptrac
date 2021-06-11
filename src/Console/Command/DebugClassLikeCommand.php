<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\ClassLikeAnalyzer;
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

    /** @var ClassLikeAnalyzer */
    private $analyzer;
    /** @var Loader */
    private $loader;

    public function __construct(
        ClassLikeAnalyzer $analyzer,
        Loader $loader
    ) {
        parent::__construct();

        $this->analyzer = $analyzer;
        $this->loader = $loader;
    }

    protected function configure(): void
    {
        $this->addArgument('depfile', InputArgument::REQUIRED, 'Path to the depfile');
        $this->addArgument('class-like', InputArgument::REQUIRED, 'Full qualified class-like name to debug');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new Style(new SymfonyStyle($input, $output));

        $depfile = $input->getArgument('depfile');

        if (!is_string($depfile)) {
            throw SingleDepfileIsRequiredException::fromArgument($depfile);
        }

        /** @var string $classLike */
        $classLike = $input->getArgument('class-like');

        $configuration = $this->loader->load($depfile);

        $matchedClassLikeNames = $this->analyzer->analyze($configuration, ClassLikeName::fromFQCN($classLike));

        natcasesort($matchedClassLikeNames);

        $style->table([$classLike], array_map(static function (string $matchedClassLikeName): array {
            return [$matchedClassLikeName];
        }, $matchedClassLikeNames));

        return 0;
    }
}
