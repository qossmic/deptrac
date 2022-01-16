<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Console;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstParser\Cache\AstFileReferenceCacheInterface;
use Qossmic\Deptrac\AstRunner\AstParser\Cache\AstFileReferenceDeferredCacheInterface;
use Qossmic\Deptrac\AstRunner\AstParser\Cache\AstFileReferenceFileCache;
use Qossmic\Deptrac\AstRunner\AstParser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Collector\Registry;
use Qossmic\Deptrac\Console\ServiceContainerBuilder;
use const DIRECTORY_SEPARATOR;

final class ServiceContainerBuilderTest extends TestCase
{
    public function testBuildsContainerWithDefaultParameters(): void
    {
        $builder = new ServiceContainerBuilder(__DIR__);

        $container = $builder->build();

        self::assertFalse($container->getParameter('ignore_uncovered_internal_classes'));
        self::assertSame(
            ['class', 'use'],
            $container->getParameter('analyser.types')
        );
        self::assertNull($container->getParameter('baseline'));
        self::assertSame(
            [],
            $container->getParameter('paths')
        );
        self::assertSame(
            [],
            $container->getParameter('exclude_files')
        );
        self::assertSame(
            [],
            $container->getParameter('layers')
        );
        self::assertSame(
            [],
            $container->getParameter('ruleset')
        );
        self::assertSame(
            [],
            $container->getParameter('skip_violations')
        );

        self::assertFalse($container->has(AstFileReferenceDeferredCacheInterface::class));
        self::assertInstanceOf(
            AstFileReferenceInMemoryCache::class,
            $container->get(AstFileReferenceCacheInterface::class)
        );
    }

    public function testConfigOverridesDefaults(): void
    {
        $expectedLayers = [
            'Dummy' => [
                ['name' => 'dummy'],
            ],
        ];
        $builder = (new ServiceContainerBuilder(__DIR__))
            ->withConfig(__DIR__.DIRECTORY_SEPARATOR.'deptrac_with_service.yaml');

        $container = $builder->build();

        self::assertSame($expectedLayers, $container->getParameter('layers'));

        /** @var Registry $collectorRegistry */
        $collectorRegistry = $container->get(Registry::class);

        $collector = $collectorRegistry->getCollector('dummy');

        self::assertInstanceOf(DummyCollector::class, $collector);
    }

    public function testCacheRegistersFileBasedCache(): void
    {
        $builder = (new ServiceContainerBuilder(__DIR__))
            ->withCache(__DIR__.DIRECTORY_SEPARATOR.'deptrac_test.cache');

        $container = $builder->build();

        self::assertTrue($container->has(AstFileReferenceDeferredCacheInterface::class));
        self::assertInstanceOf(
            AstFileReferenceFileCache::class,
            $container->get(AstFileReferenceCacheInterface::class)
        );
    }
}
