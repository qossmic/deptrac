<?php

namespace Symfony\Config\Deptrac;

require_once __DIR__.\DIRECTORY_SEPARATOR.'LayersConfig'.\DIRECTORY_SEPARATOR.'CollectorsConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class LayersConfig 
{
    private $name;
    private $collectors;
    private $attributes;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function name($value): static
    {
        $this->_usedProperties['name'] = true;
        $this->name = $value;

        return $this;
    }

    public function collectors(array $value = []): \Symfony\Config\Deptrac\LayersConfig\CollectorsConfig
    {
        $this->_usedProperties['collectors'] = true;

        return $this->collectors[] = new \Symfony\Config\Deptrac\LayersConfig\CollectorsConfig($value);
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function attributes(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['attributes'] = true;
        $this->attributes = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('name', $value)) {
            $this->_usedProperties['name'] = true;
            $this->name = $value['name'];
            unset($value['name']);
        }

        if (array_key_exists('collectors', $value)) {
            $this->_usedProperties['collectors'] = true;
            $this->collectors = array_map(function ($v) { return new \Symfony\Config\Deptrac\LayersConfig\CollectorsConfig($v); }, $value['collectors']);
            unset($value['collectors']);
        }

        if (array_key_exists('attributes', $value)) {
            $this->_usedProperties['attributes'] = true;
            $this->attributes = $value['attributes'];
            unset($value['attributes']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['name'])) {
            $output['name'] = $this->name;
        }
        if (isset($this->_usedProperties['collectors'])) {
            $output['collectors'] = array_map(function ($v) { return $v->toArray(); }, $this->collectors);
        }
        if (isset($this->_usedProperties['attributes'])) {
            $output['attributes'] = $this->attributes;
        }

        return $output;
    }

}
