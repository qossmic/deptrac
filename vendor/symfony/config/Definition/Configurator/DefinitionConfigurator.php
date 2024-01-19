<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\Config\Definition\Configurator;

use DEPTRAC_202401\Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use DEPTRAC_202401\Symfony\Component\Config\Definition\Builder\NodeDefinition;
use DEPTRAC_202401\Symfony\Component\Config\Definition\Builder\TreeBuilder;
use DEPTRAC_202401\Symfony\Component\Config\Definition\Loader\DefinitionFileLoader;
/**
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class DefinitionConfigurator
{
    public function __construct(private TreeBuilder $treeBuilder, private DefinitionFileLoader $loader, private string $path, private string $file)
    {
    }
    public function import(string $resource, string $type = null, bool $ignoreErrors = \false) : void
    {
        $this->loader->setCurrentDir(\dirname($this->path));
        $this->loader->import($resource, $type, $ignoreErrors, $this->file);
    }
    public function rootNode() : NodeDefinition|ArrayNodeDefinition
    {
        return $this->treeBuilder->getRootNode();
    }
    public function setPathSeparator(string $separator) : void
    {
        $this->treeBuilder->setPathSeparator($separator);
    }
}
