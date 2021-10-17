<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console;

use Psr\Container\ContainerInterface;
use Qossmic\Deptrac\Console\Command\AnalyseCommand;
use Qossmic\Deptrac\Console\Command\DebugLayerCommand;
use Qossmic\Deptrac\Console\Command\DebugTokenCommand;
use Qossmic\Deptrac\Console\Command\DebugUnassignedCommand;
use Qossmic\Deptrac\Console\Command\InitCommand;
use Qossmic\Deptrac\ContainerBuilder;
use Qossmic\Deptrac\Exception\ShouldNotHappenException;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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

        /** @var AnalyseCommand $analyseCommand */
        $analyseCommand = $container->get(AnalyseCommand::class);

        /** @var DebugTokenCommand $debugClassLikeCommand */
        $debugClassLikeCommand = $container->get(DebugTokenCommand::class);

        /** @var DebugLayerCommand $debugLayerCommand */
        $debugLayerCommand = $container->get(DebugLayerCommand::class);

        /** @var DebugUnassignedCommand $debugUnassignedCommand */
        $debugUnassignedCommand = $container->get(DebugUnassignedCommand::class);

        $this->addCommands([$initCommand, $analyseCommand, $debugClassLikeCommand, $debugLayerCommand, $debugUnassignedCommand]);
        $this->setDefaultCommand('analyse');

        return parent::doRun($input, $output);
    }

    private function buildContainer(InputInterface $input, string $currentWorkingDirectory): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder($currentWorkingDirectory);

        if (false === $input->hasParameterOption('--no-cache')) {
            /** @var string $cacheFile */
            $cacheFile = $input->getParameterOption('--cache-file', $currentWorkingDirectory.'/.deptrac.cache');
            $containerBuilder->useCache($cacheFile);
        }

        return $containerBuilder->build();
    }
}
