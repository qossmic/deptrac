<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\Config\Definition;

use DEPTRAC_202401\Symfony\Component\Config\Definition\Builder\TreeBuilder;
use DEPTRAC_202401\Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use DEPTRAC_202401\Symfony\Component\Config\Definition\Loader\DefinitionFileLoader;
use DEPTRAC_202401\Symfony\Component\Config\FileLocator;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\ContainerBuilder;
/**
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 *
 * @final
 */
class Configuration implements ConfigurationInterface
{
    public function __construct(private ConfigurableInterface $subject, private ?ContainerBuilder $container, private string $alias)
    {
    }
    public function getConfigTreeBuilder() : TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->alias);
        $file = (new \ReflectionObject($this->subject))->getFileName();
        $loader = new DefinitionFileLoader($treeBuilder, new FileLocator(\dirname($file)), $this->container);
        $configurator = new DefinitionConfigurator($treeBuilder, $loader, $file, $file);
        $this->subject->configure($configurator);
        return $treeBuilder;
    }
}
