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

use DEPTRAC_202401\Symfony\Component\Config\Definition\EnumNode;
/**
 * Enum Node Definition.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class EnumNodeDefinition extends ScalarNodeDefinition
{
    private array $values;
    /**
     * @return $this
     */
    public function values(array $values) : static
    {
        if (!$values) {
            throw new \InvalidArgumentException('->values() must be called with at least one value.');
        }
        $this->values = $values;
        return $this;
    }
    /**
     * Instantiate a Node.
     *
     * @throws \RuntimeException
     */
    protected function instantiateNode() : EnumNode
    {
        if (!isset($this->values)) {
            throw new \RuntimeException('You must call ->values() on enum nodes.');
        }
        return new EnumNode($this->name, $this->parent, $this->values, $this->pathSeparator);
    }
}
