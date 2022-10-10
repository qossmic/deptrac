<?php

namespace Symfony\Config\Deptrac;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Formatters'.\DIRECTORY_SEPARATOR.'GraphvizConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Formatters'.\DIRECTORY_SEPARATOR.'CodeclimateConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class FormattersConfig 
{
    private $graphviz;
    private $codeclimate;
    private $_usedProperties = [];

    /**
     * Configure Graphviz output formatters
     * @default {"hidden_layers":[],"groups":[],"point_to_groups":false}
     * @return \Symfony\Config\Deptrac\Formatters\GraphvizConfig|$this
     */
    public function graphviz(mixed $value = []): \Symfony\Config\Deptrac\Formatters\GraphvizConfig|static
    {
        if (!\is_array($value)) {
            $this->_usedProperties['graphviz'] = true;
            $this->graphviz = $value;

            return $this;
        }

        if (!$this->graphviz instanceof \Symfony\Config\Deptrac\Formatters\GraphvizConfig) {
            $this->_usedProperties['graphviz'] = true;
            $this->graphviz = new \Symfony\Config\Deptrac\Formatters\GraphvizConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "graphviz()" has already been initialized. You cannot pass values the second time you call graphviz().');
        }

        return $this->graphviz;
    }

    /**
     * Configure Codeclimate output formatters
     * @default {"severity":{"failure":"major","skipped":"minor","uncovered":"info"}}
    */
    public function codeclimate(array $value = []): \Symfony\Config\Deptrac\Formatters\CodeclimateConfig
    {
        if (null === $this->codeclimate) {
            $this->_usedProperties['codeclimate'] = true;
            $this->codeclimate = new \Symfony\Config\Deptrac\Formatters\CodeclimateConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "codeclimate()" has already been initialized. You cannot pass values the second time you call codeclimate().');
        }

        return $this->codeclimate;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('graphviz', $value)) {
            $this->_usedProperties['graphviz'] = true;
            $this->graphviz = \is_array($value['graphviz']) ? new \Symfony\Config\Deptrac\Formatters\GraphvizConfig($value['graphviz']) : $value['graphviz'];
            unset($value['graphviz']);
        }

        if (array_key_exists('codeclimate', $value)) {
            $this->_usedProperties['codeclimate'] = true;
            $this->codeclimate = new \Symfony\Config\Deptrac\Formatters\CodeclimateConfig($value['codeclimate']);
            unset($value['codeclimate']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['graphviz'])) {
            $output['graphviz'] = $this->graphviz instanceof \Symfony\Config\Deptrac\Formatters\GraphvizConfig ? $this->graphviz->toArray() : $this->graphviz;
        }
        if (isset($this->_usedProperties['codeclimate'])) {
            $output['codeclimate'] = $this->codeclimate->toArray();
        }

        return $output;
    }

}
