<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Console;

use Qossmic\Deptrac\Supportive\DependencyInjection\ServiceContainerBuilder;
use Qossmic\Deptrac\Supportive\ShouldNotHappenException;
use RuntimeException;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function getcwd;
use function in_array;
use const DIRECTORY_SEPARATOR;

final class Application extends BaseApplication
{
    public const VERSION = '@git-version@';

    public function __construct()
    {
        parent::__construct('deptrac', self::VERSION);
    }

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $definition = parent::getDefaultInputDefinition();

        $definition->addOptions([
            new InputOption(
                '--no-cache',
                null,
                InputOption::VALUE_NONE,
                'Disable caching mechanisms (wins over --cache-file)'
            ),
            new InputOption(
                '--clear-cache',
                null,
                InputOption::VALUE_NONE,
                'Clears cache file before run'
            ),
            new InputOption(
                '--cache-file',
                null,
                InputOption::VALUE_REQUIRED,
                'Location where cache file will be stored',
                getcwd().DIRECTORY_SEPARATOR.'.deptrac.cache'
            ),
            new InputOption(
                '--config-file',
                '-c',
                InputOption::VALUE_REQUIRED,
                'Location of Depfile containing the configuration',
                getcwd().DIRECTORY_SEPARATOR.'deptrac.yaml'
            ),
        ]);

        return $definition;
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        if (false === ($currentWorkingDirectory = getcwd())) {
            throw new ShouldNotHappenException();
        }

        try {
            $input->bind($this->getDefinition());
        } catch (ExceptionInterface $e) {
            // Errors must be ignored, full binding/validation happens later when the command is known.
        }

        if (null === $input->getArgument('command') && true === $input->getOption('version')) {
            return parent::doRun($input, $output);
        }

        /** @var string|numeric|null $configFile */
        $configFile = $input->getParameterOption('--config-file', $currentWorkingDirectory.DIRECTORY_SEPARATOR.'deptrac.yaml');
        $config = $input->hasParameterOption('--config-file')
            ? (string) $configFile
            : $currentWorkingDirectory.DIRECTORY_SEPARATOR.'deptrac.yaml';

        /** @var string|numeric|null $cacheFile */
        $cacheFile = $input->getParameterOption('--cache-file', $currentWorkingDirectory.DIRECTORY_SEPARATOR.'.deptrac.cache');
        $cache = $input->hasParameterOption('--cache-file')
            ? (string) $cacheFile
            : $currentWorkingDirectory.DIRECTORY_SEPARATOR.'.deptrac.cache';

        $factory = new ServiceContainerBuilder($currentWorkingDirectory);
        if ($input->hasParameterOption('--clear-cache', true)) {
            $factory = $factory->clearCache($cache);
        }
        if (!in_array($input->getArgument('command'), ['init', 'list', 'help', 'completion'], true)) {
            $factory = $factory->withConfig($config);
        }
        if (false === $input->hasParameterOption('--no-cache', true)) {
            $factory = $factory->withCache($cache);
        }

        $container = $factory->build();

        $commandLoader = $container->get('console.command_loader');
        if (!$commandLoader instanceof CommandLoaderInterface) {
            throw new RuntimeException('CommandLoader not initialized. Commands can not be registered.');
        }
        $this->setCommandLoader($commandLoader);
        $this->setDefaultCommand('analyse');

        return parent::doRun($input, $output);
    }
}
