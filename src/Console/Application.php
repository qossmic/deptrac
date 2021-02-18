<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console;

use Qossmic\Deptrac\Console\Command\AnalyzeCommand;
use Qossmic\Deptrac\Console\Command\InitCommand;
use Qossmic\Deptrac\ShouldNotHappenException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class Application extends BaseApplication
{
    public const VERSION = '@git-version@';

    public function __construct()
    {
        parent::__construct('deptrac', self::VERSION);

        $this->getDefinition()->addOptions([
            new InputOption('--no-cache', null, InputOption::VALUE_NONE, 'Disable caching mechanisms'),
            new InputOption('--cache-file', null, InputOption::VALUE_REQUIRED, '', '.deptrac.cache'),
        ]);
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        if (false === ($currentWorkingDirectory = getcwd())) {
            throw new ShouldNotHappenException();
        }

        $container = $this->buildContainer($input, $currentWorkingDirectory);

        /** @var InitCommand $initCommand */
        $initCommand = $container->get(InitCommand::class);

        /** @var AnalyzeCommand $analyzeCommand */
        $analyzeCommand = $container->get(AnalyzeCommand::class);

        $this->addCommands([$initCommand, $analyzeCommand]);
        $this->setDefaultCommand('analyze');

        return parent::doRun($input, $output);
    }

    private function buildContainer(InputInterface $input, string $currentWorkingDirectory): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('currentWorkingDirectory', $currentWorkingDirectory);
        $container->addCompilerPass(
            new RegisterListenersPass(EventDispatcher::class, 'event_listener', 'event_subscriber')
        );

        $fileLoader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config/'));
        $fileLoader->load('services.php');

        if (false === $input->hasParameterOption('--no-cache')) {
            $container->setParameter(
                'deptrac.cache_file',
                $input->getParameterOption('--cache-file', getcwd().'/.deptrac.cache')
            );

            $fileLoader->load('cache.php');
        }

        $container->compile();

        return $container;
    }
}
