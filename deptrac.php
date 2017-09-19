<?php

require __DIR__.'/vendor/autoload.php';

use SensioLabs\Deptrac\CompilerPass\CollectorPass;
use SensioLabs\Deptrac\CompilerPass\OutputFormatterPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;


if (!version_compare(PHP_VERSION, '5.6.0', '>=')) {
    echo 'Required at least PHP version 5.6.0, your version: '.PHP_VERSION."\n";
    die(1);
}

$container = new ContainerBuilder();

(new XmlFileLoader($container, new FileLocator(__DIR__)))->load(__DIR__.'/services.xml');

$container
    ->addCompilerPass(new OutputFormatterPass())
    ->addCompilerPass(new CollectorPass())
    ->compile();

$container->set('container', $container);

$application = new Application();
$application->add($container->get('command_init'));
$application->add($container->get('command_analyze'));
$application->add($container->get('command_self_update'));
$application->setDefaultCommand('analyze');
$application->run();
