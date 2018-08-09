<?php

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../autoload.php')) {
    require __DIR__ . '/../../autoload.php';
} else {
    die(
        'You must set up the project dependencies, run the following commands:'.PHP_EOL.
        'curl -s http://getcomposer.org/installer | php'.PHP_EOL.
        'php composer.phar install'.PHP_EOL
    );
}

use SensioLabs\Deptrac\Command\AnalyzeCommand;
use SensioLabs\Deptrac\Command\InitCommand;
use SensioLabs\Deptrac\Command\SelfUpdateCommand;
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
$application->add($container->get(InitCommand::class));
$application->add($container->get(AnalyzeCommand::class));
$application->add($container->get(SelfUpdateCommand::class));
$application->setDefaultCommand('analyze');
$application->run();
