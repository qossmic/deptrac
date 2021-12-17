<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->parameters()->set('ignore_uncovered_internal_classes', false);
    $container->parameters()->set('supported_analyser_types', ['class', 'use']);
    $container->parameters()->set('baseline', null);
    $container->parameters()->set('paths', []);
    $container->parameters()->set('exclude_files', []);
    $container->parameters()->set('layers', []);
    $container->parameters()->set('ruleset', []);
    $container->parameters()->set('skip_violations', []);
};
