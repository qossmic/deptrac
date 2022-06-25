<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\DependencyInjection;

use Qossmic\Deptrac\DependencyInjection\Exception\CacheFileException;
use SplFileInfo;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Path;

final class ServiceContainerBuilder
{
    private string $workingDirectory;

    private ?SplFileInfo $configFile = null;

    private ?SplFileInfo $cacheFile = null;

    public function __construct(string $workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
    }

    public function withConfig(?string $configFile): self
    {
        if (null === $configFile) {
            return $this;
        }

        $builder = clone $this;

        if (Path::isRelative($configFile)) {
            $configFile = Path::makeAbsolute($configFile, $this->workingDirectory);
        }

        $builder->configFile = new SplFileInfo($configFile);

        return $builder;
    }

    public function withCache(?string $cacheFile): self
    {
        if (null === $cacheFile) {
            return $this;
        }

        $builder = clone $this;

        if (Path::isRelative($cacheFile)) {
            $cacheFile = Path::makeAbsolute($cacheFile, $this->workingDirectory);
        }

        $builder->cacheFile = new SplFileInfo($cacheFile);

        return $builder;
    }

    public function clearCache(?string $cacheFile): self
    {
        if (null === $cacheFile) {
            return $this;
        }

        $builder = clone $this;

        if (Path::isRelative($cacheFile)) {
            $cacheFile = Path::makeAbsolute($cacheFile, $this->workingDirectory);
        }

        unlink($cacheFile);

        return $builder;
    }

    public function build(): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $container->setParameter('currentWorkingDirectory', $this->workingDirectory);

        self::registerCompilerPasses($container);
        self::loadServices($container, $this->cacheFile);

        $container->registerExtension(new DeptracExtension());

        $container->setParameter('depfileDirectory', $this->workingDirectory);
        if (null !== $this->configFile) {
            self::loadConfiguration($container, $this->configFile);
        }

        $container->compile(true);

        return $container;
    }

    private static function registerCompilerPasses(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AddConsoleCommandPass());

        $container->addCompilerPass(
            new RegisterListenersPass(EventDispatcher::class, 'event_listener', 'event_subscriber')
        );
    }

    private static function loadServices(ContainerBuilder $container, ?SplFileInfo $cacheFile): void
    {
        $loader = new PhpFileLoader($container, new FileLocator([__DIR__.'/../../config']));

        $loader->load('services.php');

        if (!$cacheFile instanceof SplFileInfo) {
            return;
        }

        if (!file_exists($cacheFile->getPathname())
            && !touch($cacheFile->getPathname())
            && !is_writable($cacheFile->getPathname())
        ) {
            throw CacheFileException::notWritable($cacheFile);
        }

        $container->setParameter('deptrac.cache_file', $cacheFile->getPathname());
        $loader->load('cache.php');
    }

    private static function loadConfiguration(ContainerBuilder $container, SplFileInfo $configFile): void
    {
        $container->setParameter('depfileDirectory', $configFile->getPathInfo()->getPathname());

        $loader = new DelegatingLoader(new LoaderResolver([
            new YamlFileLoader($container, new FileLocator([$configFile->getPathInfo()->getPathname()])),
            new PhpFileLoader($container, new FileLocator([$configFile->getPathInfo()->getPathname()])),
        ]));

        $loader->load($configFile->getFilename());
    }
}
