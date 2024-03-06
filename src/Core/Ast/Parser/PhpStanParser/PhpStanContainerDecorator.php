<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser;

use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\DependencyInjection\Container;
use PHPStan\DependencyInjection\ContainerFactory;
use PHPStan\File\FileHelper;
use PHPStan\Parser\Parser;
use PHPStan\PhpDoc\TypeNodeResolver;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\FileTypeMapper;

class PhpStanContainerDecorator
{
    private Container $container;

    /**
     * @param list<string> $paths
     */
    public function __construct(string $basePath, array $paths)
    {
        $factory = new ContainerFactory($basePath);
        $this->container = $factory->create(sys_get_temp_dir(), [
            __DIR__.'/config/config.neon',
            __DIR__.'/config/parser.neon',
        ], $paths);
    }

    /**
     * @api
     */
    public function createReflectionProvider(): ReflectionProvider
    {
        return $this->container->getByType(ReflectionProvider::class);
    }

    /**
     * @api
     */
    public function createPHPStanParser(): Parser
    {
        $service = $this->container->getService('currentPhpVersionRichParser');
        assert($service instanceof Parser);

        return $service;
    }

    /**
     * @api
     */
    public function createNodeScopeResolver(): NodeScopeResolver
    {
        return $this->container->getByType(NodeScopeResolver::class);
    }

    /**
     * @api
     */
    public function createScopeFactory(): ScopeFactory
    {
        return $this->container->getByType(ScopeFactory::class);
    }

    /**
     * @template TObject as Object
     *
     * @param class-string<TObject> $type
     *
     * @return TObject
     */
    public function getByType(string $type): object
    {
        return $this->container->getByType($type);
    }

    /**
     * @api
     */
    public function createFileHelper(): FileHelper
    {
        return $this->container->getByType(FileHelper::class);
    }

    /**
     * @api
     */
    public function createFileTypeMapper(): FileTypeMapper
    {
        return $this->container->getByType(FileTypeMapper::class);
    }

    /**
     * @api
     */
    public function createTypeNodeResolver(): TypeNodeResolver
    {
        return $this->container->getByType(TypeNodeResolver::class);
    }
}
