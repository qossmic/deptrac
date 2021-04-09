<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class ContainerBuilder
{
    /** @var string */
    private $currentWorkingDirectory;
    /** @var string|null */
    private $cacheFile;

    public function __construct(string $currentWorkingDirectory)
    {
        $this->currentWorkingDirectory = $currentWorkingDirectory;
    }

    public function useCache(string $cacheFile): self
    {
        $this->cacheFile = $cacheFile;

        return $this;
    }

    public function build(): ContainerInterface
    {
        $container = new SymfonyContainerBuilder();
        $container->setParameter('currentWorkingDirectory', $this->currentWorkingDirectory);
        $container->addCompilerPass(
            new RegisterListenersPass(EventDispatcher::class, 'event_listener', 'event_subscriber')
        );

        $fileLoader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../config/'));
        $fileLoader->load('services.php');

        if (null !== $this->cacheFile) {
            $container->setParameter('deptrac.cache_file', $this->cacheFile);
            $fileLoader->load('cache.php');
        }

        $container->compile();

        return $container;
    }
}
