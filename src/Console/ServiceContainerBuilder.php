<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console;

use Qossmic\Deptrac\Console\Exception\CacheFileException;
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

    public function build(): ContainerBuilder
    {
        $builder = new ContainerBuilder();
        $builder->setParameter('currentWorkingDirectory', $this->workingDirectory);

        self::registerCompilerPasses($builder);
        self::loadServices($builder, $this->cacheFile);

        $builder->setParameter('depfileDirectory', $this->workingDirectory);
        if (null !== $this->configFile) {
            self::loadConfiguration($builder, $this->configFile);
        }

        $builder->compile(true);

        return $builder;
    }

    private static function registerCompilerPasses(ContainerBuilder $builder): void
    {
        $builder->addCompilerPass(new AddConsoleCommandPass());
    }

    private static function loadServices(ContainerBuilder $builder, ?SplFileInfo $cacheFile): void
    {
        $loader = new PhpFileLoader($builder, new FileLocator([__DIR__.'/../../config']));
        $loader->load('parameters.php');
        $loader->load('services.php');

        $builder->addCompilerPass(
            new RegisterListenersPass(EventDispatcher::class, 'event_listener', 'event_subscriber')
        );

        if (!$cacheFile instanceof SplFileInfo) {
            return;
        }

        if (!file_exists($cacheFile->getPathname())
            && !touch($cacheFile->getPathname())
            && !is_writable($cacheFile->getPathname())
        ) {
            throw CacheFileException::notWritable($cacheFile);
        }

        $builder->setParameter('deptrac.cache_file', $cacheFile->getPathname());
        $loader->load('cache.php');
    }

    private static function loadConfiguration(ContainerBuilder $builder, SplFileInfo $configFile): void
    {
        $builder->setParameter('depfileDirectory', $configFile->getPathInfo()->getPathname());

        $loader = new DelegatingLoader(new LoaderResolver([
            new YamlFileLoader($builder, new FileLocator([$configFile->getPathInfo()->getPathname()])),
            new PhpFileLoader($builder, new FileLocator([$configFile->getPathInfo()->getPathname()])),
        ]));

        $loader->load($configFile->getFilename());
    }
}
