<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Supportive\DependencyInjection;

use Exception;
use Qossmic\Deptrac\Supportive\DependencyInjection\Exception\CacheFileException;
use Qossmic\Deptrac\Supportive\DependencyInjection\Exception\CannotLoadConfiguration;
use SplFileInfo;
use DEPTRAC_202403\Symfony\Component\Config\Builder\ConfigBuilderGenerator;
use DEPTRAC_202403\Symfony\Component\Config\FileLocator;
use DEPTRAC_202403\Symfony\Component\Config\Loader\DelegatingLoader;
use DEPTRAC_202403\Symfony\Component\Config\Loader\LoaderResolver;
use DEPTRAC_202403\Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use DEPTRAC_202403\Symfony\Component\DependencyInjection\ContainerBuilder;
use DEPTRAC_202403\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use DEPTRAC_202403\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use DEPTRAC_202403\Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use DEPTRAC_202403\Symfony\Component\Filesystem\Path;
final class ServiceContainerBuilder
{
    private ?SplFileInfo $configFile = null;
    private ?SplFileInfo $cacheFile = null;
    public function __construct(private readonly string $workingDirectory)
    {
    }
    public function withConfig(?string $configFile) : self
    {
        if (null === $configFile) {
            return $this;
        }
        $builder = clone $this;
        if (Path::isRelative($configFile)) {
            /** @throws void */
            $configFile = Path::makeAbsolute($configFile, $this->workingDirectory);
        }
        $builder->configFile = new SplFileInfo($configFile);
        return $builder;
    }
    public function withCache(?string $cacheFile) : self
    {
        if (null === $cacheFile) {
            return $this;
        }
        $builder = clone $this;
        if (Path::isRelative($cacheFile)) {
            /** @throws void */
            $cacheFile = Path::makeAbsolute($cacheFile, $this->workingDirectory);
        }
        $builder->cacheFile = new SplFileInfo($cacheFile);
        return $builder;
    }
    public function clearCache(?string $cacheFile) : self
    {
        if (null === $cacheFile) {
            return $this;
        }
        $builder = clone $this;
        if (Path::isRelative($cacheFile)) {
            /** @throws void */
            $cacheFile = Path::makeAbsolute($cacheFile, $this->workingDirectory);
        }
        \unlink($cacheFile);
        return $builder;
    }
    /**
     * @throws CacheFileException
     * @throws CannotLoadConfiguration
     */
    public function build() : ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('currentWorkingDirectory', $this->workingDirectory);
        self::registerCompilerPasses($container);
        self::loadServices($container, $this->cacheFile);
        $container->registerExtension(new \Qossmic\Deptrac\Supportive\DependencyInjection\DeptracExtension());
        $container->setParameter('projectDirectory', $this->workingDirectory);
        if (null !== $this->configFile) {
            self::loadConfiguration($container, $this->configFile);
        }
        $container->compile(\true);
        return $container;
    }
    private static function registerCompilerPasses(ContainerBuilder $container) : void
    {
        $container->addCompilerPass(new AddConsoleCommandPass());
        $container->addCompilerPass(new RegisterListenersPass());
    }
    /**
     * @throws CacheFileException
     * @throws CannotLoadConfiguration
     */
    private static function loadServices(ContainerBuilder $container, ?SplFileInfo $cacheFile) : void
    {
        $loader = new PhpFileLoader($container, new FileLocator([__DIR__ . '/../../../config']));
        try {
            $loader->load('services.php');
        } catch (Exception $exception) {
            throw CannotLoadConfiguration::fromServices('services.php', $exception->getMessage());
        }
        if (!$cacheFile instanceof SplFileInfo) {
            return;
        }
        if (!\file_exists($cacheFile->getPathname())) {
            $dirname = $cacheFile->getPath() ?: '.';
            if (!\is_dir($dirname) && \mkdir($dirname . '/', 0777, \true) && !\is_dir($dirname)) {
                throw CacheFileException::notWritable($cacheFile);
            }
            if (!\touch($cacheFile->getPathname()) && !\is_writable($cacheFile->getPathname())) {
                throw CacheFileException::notWritable($cacheFile);
            }
        }
        $container->setParameter('deptrac.cache_file', $cacheFile->getPathname());
        try {
            $loader->load('cache.php');
        } catch (Exception $exception) {
            throw CannotLoadConfiguration::fromCache('cache.php', $exception->getMessage());
        }
    }
    /**
     * @throws CannotLoadConfiguration
     */
    private static function loadConfiguration(ContainerBuilder $container, SplFileInfo $configFile) : void
    {
        $configPathInfo = $configFile->getPathInfo();
        /** @phpstan-ignore-next-line false positive */
        if (null === $configPathInfo) {
            throw CannotLoadConfiguration::fromConfig($configFile->getFilename(), 'Unable to load config: Invalid or missing path.');
        }
        $container->setParameter('projectDirectory', $configPathInfo->getPathname());
        $loader = new DelegatingLoader(new LoaderResolver([new YamlFileLoader($container, new FileLocator([$configPathInfo->getPathname()])), new PhpFileLoader($container, new FileLocator([$configPathInfo->getPathname()]), generator: new ConfigBuilderGenerator('.'))]));
        try {
            $loader->load($configFile->getFilename());
        } catch (Exception $exception) {
            throw CannotLoadConfiguration::fromConfig($configFile->getFilename(), $exception->getMessage());
        }
    }
}
