<?php

namespace Symfony\Config\Deptrac\Formatters;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class GraphvizConfig 
{
    private $hiddenLayers;
    private $groups;
    private $pointToGroups;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function hiddenLayers(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['hiddenLayers'] = true;
        $this->hiddenLayers = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function groups(string $name, ParamConfigurator|array $value): static
    {
        $this->_usedProperties['groups'] = true;
        $this->groups[$name] = $value;

        return $this;
    }

    /**
     * When a layer is part of a group, should edges point towards the group or the layer?
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function pointToGroups($value): static
    {
        $this->_usedProperties['pointToGroups'] = true;
        $this->pointToGroups = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('hidden_layers', $value)) {
            $this->_usedProperties['hiddenLayers'] = true;
            $this->hiddenLayers = $value['hidden_layers'];
            unset($value['hidden_layers']);
        }

        if (array_key_exists('groups', $value)) {
            $this->_usedProperties['groups'] = true;
            $this->groups = $value['groups'];
            unset($value['groups']);
        }

        if (array_key_exists('point_to_groups', $value)) {
            $this->_usedProperties['pointToGroups'] = true;
            $this->pointToGroups = $value['point_to_groups'];
            unset($value['point_to_groups']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['hiddenLayers'])) {
            $output['hidden_layers'] = $this->hiddenLayers;
        }
        if (isset($this->_usedProperties['groups'])) {
            $output['groups'] = $this->groups;
        }
        if (isset($this->_usedProperties['pointToGroups'])) {
            $output['point_to_groups'] = $this->pointToGroups;
        }

        return $output;
    }

}
