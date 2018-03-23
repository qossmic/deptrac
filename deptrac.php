<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

if (PHP_VERSION_ID < 70000) {
    echo 'Required at least PHP version 7.0.0, your version: '.PHP_VERSION."\n";
    die(1);
}

$container = new ContainerBuilder();
(new XmlFileLoader($container, new FileLocator(__DIR__)))->load('services.xml');
$container->compile();

$application = new Application();
$application->add($container->get('command_init'));
$application->add($container->get('command_analyze'));
$application->add($container->get('command_self_update'));
$application->setDefaultCommand('analyze');
$application->run();
