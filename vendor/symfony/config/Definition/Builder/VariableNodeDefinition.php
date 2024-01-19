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

use DEPTRAC_202401\Symfony\Component\Config\Definition\NodeInterface;
use DEPTRAC_202401\Symfony\Component\Config\Definition\VariableNode;
/**
 * This class provides a fluent interface for defining a node.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class VariableNodeDefinition extends NodeDefinition
{
    /**
     * Instantiate a Node.
     */
    protected function instantiateNode() : VariableNode
    {
        return new VariableNode($this->name, $this->parent, $this->pathSeparator);
    }
    protected function createNode() : NodeInterface
    {
        $node = $this->instantiateNode();
        if (isset($this->normalization)) {
            $node->setNormalizationClosures($this->normalization->before);
        }
        if (isset($this->merge)) {
            $node->setAllowOverwrite($this->merge->allowOverwrite);
        }
        if (\true === $this->default) {
            $node->setDefaultValue($this->defaultValue);
        }
        $node->setAllowEmptyValue($this->allowEmptyValue);
        $node->addEquivalentValue(null, $this->nullEquivalent);
        $node->addEquivalentValue(\true, $this->trueEquivalent);
        $node->addEquivalentValue(\false, $this->falseEquivalent);
        $node->setRequired($this->required);
        if ($this->deprecation) {
            $node->setDeprecated($this->deprecation['package'], $this->deprecation['version'], $this->deprecation['message']);
        }
        if (isset($this->validation)) {
            $node->setFinalValidationClosures($this->validation->rules);
        }
        return $node;
    }
}
