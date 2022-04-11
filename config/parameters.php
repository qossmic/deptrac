<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $container): void {
    $container->parameters()->set('projectDirectory', param('depfileDirectory'));

    $container->parameters()->set('ignore_uncovered_internal_classes', true);
    $container->parameters()->set('analyser', ['types' => ['class', 'use']]);
    $container->parameters()->set('paths', []);
    $container->parameters()->set('exclude_files', []);
    $container->parameters()->set('layers', []);
    $container->parameters()->set('ruleset', []);
    $container->parameters()->set('skip_violations', []);
    $container->parameters()->set('skip_layers', []);
    $container->parameters()->set('formatters', []);
};
