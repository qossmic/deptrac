<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Console;

use SensioLabs\Deptrac\Console\Command\AnalyzeCommand;
use SensioLabs\Deptrac\Console\Command\InitCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class Application extends BaseApplication
{
    public const VERSION = '@git-version@';

    public function __construct(bool $cache = true)
    {
        parent::__construct('deptrac', self::VERSION);

        $container = $this->buildContainer($cache);

        /** @var InitCommand $initCommand */
        $initCommand = $container->get(InitCommand::class);

        /** @var AnalyzeCommand $analyzeCommand */
        $analyzeCommand = $container->get(AnalyzeCommand::class);

        $this->addCommands([$initCommand, $analyzeCommand]);

        $this->setDefaultCommand('analyze');
    }

    private function buildContainer(bool $cache): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(
            new RegisterListenersPass(EventDispatcher::class, 'event_listener', 'event_subscriber')
        );

        $fileLoader = new XmlFileLoader($container, new FileLocator(__DIR__));
        $fileLoader->load(__DIR__.'/../../config/services.xml');

        if (true === $cache) {
            $fileLoader->load(__DIR__.'/../../config/cache.xml');
        }

        $container->compile();

        return $container;
    }
}
