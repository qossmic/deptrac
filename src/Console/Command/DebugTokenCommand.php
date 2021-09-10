<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileName;
use Qossmic\Deptrac\AstRunner\AstMap\FunctionName;
use Qossmic\Deptrac\Configuration\Loader;
use Qossmic\Deptrac\Console\Command\Exception\SingleDepfileIsRequiredException;
use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\TokenAnalyser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DebugTokenCommand extends Command
{
    use DefaultDepFileTrait;

    private TokenAnalyser $analyser;
    private Loader $loader;

    public function __construct(
        TokenAnalyser $analyser,
        Loader $loader
    ) {
        parent::__construct();

        $this->analyser = $analyser;
        $this->loader = $loader;
    }

    protected function configure(): void
    {
        $this->setName('debug:token');
        $this->setAliases(['debug:class-like']);

        $this->addArgument('token', InputArgument::REQUIRED, 'Full qualified token name to debug');
        $this->addArgument('type', InputArgument::OPTIONAL, 'Token type (class-like, function, file)', 'class-like');
        $this->addOption('depfile', null, InputOption::VALUE_OPTIONAL, 'Path to the depfile');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyOutput = new SymfonyOutput($output, new Style(new SymfonyStyle($input, $output)));
        $depfile = $input->getOption('depfile') ?? $this->getDefaultFile($symfonyOutput);

        if (!is_string($depfile)) {
            throw SingleDepfileIsRequiredException::fromArgument($depfile);
        }

        /** @var string $tokenName */
        $tokenName = $input->getArgument('token');
        /** @var string $tokenType */
        $tokenType = $input->getArgument('type');

        $configuration = $this->loader->load($depfile);

        switch ($tokenType) {
            case 'class-like':
                $token = ClassLikeName::fromFQCN($tokenName);
                break;
            case 'function':
                $token = FunctionName::fromFQCN($tokenName);
                break;
            case 'file':
                $token = new FileName($tokenName);
                break;
            default:
                throw new \RuntimeException('Invalid token type "'.$tokenType.'". Only "class-like", "function" or "file" are supported.');
        }

        $layers = $this->analyser->analyse($configuration, $token);

        natcasesort($layers);

        $symfonyOutput->writeLineFormatted($layers);

        return 0;
    }
}
