<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Console;

use SensioLabs\Deptrac\Console\Command\AnalyzeCommand;
use SensioLabs\Deptrac\Console\Command\InitCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class Application extends BaseApplication
{
    public const VERSION = '@git-version@';

    public function __construct()
    {
        parent::__construct('deptrac', self::VERSION);

        $container = new ContainerBuilder();
        (new XmlFileLoader($container, new FileLocator(__DIR__)))->load(__DIR__.'/../../services.xml');
        $container->compile();

        /** @var InitCommand $initCommand */
        $initCommand = $container->get(InitCommand::class);

        /** @var AnalyzeCommand $analyzeCommand */
        $analyzeCommand = $container->get(AnalyzeCommand::class);

        $this->addCommands([$initCommand, $analyzeCommand]);

        $this->setDefaultCommand('analyze');
    }
}
