<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\Config\Definition\Builder;

use DEPTRAC_202401\Symfony\Component\Config\Definition\ScalarNode;
/**
 * This class provides a fluent interface for defining a node.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ScalarNodeDefinition extends VariableNodeDefinition
{
    /**
     * Instantiate a Node.
     */
    protected function instantiateNode() : ScalarNode
    {
        return new ScalarNode($this->name, $this->parent, $this->pathSeparator);
    }
}
