<?php

declare(strict_types=1);

use Qossmic\Deptrac\Ast\Parser\Cache\AstFileReferenceCacheInterface;
use Qossmic\Deptrac\Ast\Parser\Cache\AstFileReferenceDeferredCacheInterface;
use Qossmic\Deptrac\Ast\Parser\Cache\AstFileReferenceFileCache;
use Qossmic\Deptrac\Subscriber\CacheableFileSubscriber;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services
        ->defaults()
        ->public();

    $services
        ->set(AstFileReferenceFileCache::class)
        ->args(['%deptrac.cache_file%']);

    $services->alias(AstFileReferenceDeferredCacheInterface::class, AstFileReferenceFileCache::class);
    $services->alias(AstFileReferenceCacheInterface::class, AstFileReferenceDeferredCacheInterface::class);

    $services
        ->set(CacheableFileSubscriber::class)
        ->args([service(AstFileReferenceFileCache::class)])
        ->tag('event_subscriber');
};
