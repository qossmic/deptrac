<?php

declare(strict_types=1);

use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceCache;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceFileCache;
use SensioLabs\Deptrac\Subscriber\CacheableFileSubscriber;
use Symfony\Component\DependencyInjection\Loader\Configurator as di;

return static function (di\ContainerConfigurator $container): void {
    $services = $container->services();

    $services
        ->defaults()
        ->private();

    $services
        ->set(CacheableFileSubscriber::class)
        ->args([di\ref(AstFileReferenceCache::class)])
        ->tag('event_subscriber');

    $services
        ->set(AstFileReferenceFileCache::class)
        ->args(['%deptrac.cache_file%']);

    $services->alias(AstFileReferenceCache::class, AstFileReferenceFileCache::class);
};
