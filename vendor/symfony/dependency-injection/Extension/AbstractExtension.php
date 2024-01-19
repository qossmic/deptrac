<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\DependencyInjection\Extension;

use DEPTRAC_202401\Symfony\Component\Config\Definition\Configuration;
use DEPTRAC_202401\Symfony\Component\Config\Definition\ConfigurationInterface;
use DEPTRAC_202401\Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\ContainerBuilder;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
/**
 * An Extension that provides configuration hooks.
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
abstract class AbstractExtension extends Extension implements ConfigurableExtensionInterface, PrependExtensionInterface
{
    use ExtensionTrait;
    public function configure(DefinitionConfigurator $definition) : void
    {
    }
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder) : void
    {
    }
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder) : void
    {
    }
    public function getConfiguration(array $config, ContainerBuilder $container) : ?ConfigurationInterface
    {
        return new Configuration($this, $container, $this->getAlias());
    }
    public final function prepend(ContainerBuilder $container) : void
    {
        $callback = function (ContainerConfigurator $configurator) use($container) {
            $this->prependExtension($configurator, $container);
        };
        $this->executeConfiguratorCallback($container, $callback, $this);
    }
    public final function load(array $configs, ContainerBuilder $container) : void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $callback = function (ContainerConfigurator $configurator) use($config, $container) {
            $this->loadExtension($config, $configurator, $container);
        };
        $this->executeConfiguratorCallback($container, $callback, $this);
    }
}
