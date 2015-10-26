<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;


(new XmlFileLoader($container = new ContainerBuilder(), new FileLocator(__DIR__)))->load(__DIR__. '/services.xml');
$container->compile();

$application = new Application();
$application->add($container->get('command_init'));
$application->add($container->get('command_analyze'));
$application->setDefaultCommand('analyze');
$application->run();
