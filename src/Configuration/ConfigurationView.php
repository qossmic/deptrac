<?php

namespace DependencyTracker\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationView
{
    private $name;

    /** @var ConfigurationLayer[] */
    private $layers;

    private $ruleset;

    public static function fromArray(array $arr)
    {
        $options = (new OptionsResolver())->setRequired([
            'name',
            'layers',
            'ruleset'
        ])->resolve($arr);

        return new static(
            $options['name'],
            array_map(function($v) { return ConfigurationLayer::fromArray($v); }, $options['layers']),
            new ConfigurationRuleset($options['ruleset'])
        );
    }

    /**
     * ConfigurationViews constructor.
     * @param $name
     * @param $layers
     * @param $ruleset
     */
    public function __construct($name, $layers, $ruleset)
    {
        $this->name = $name;
        $this->layers = $layers;
        $this->ruleset = $ruleset;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ConfigurationLayer[]
     */
    public function getLayers()
    {
        return $this->layers;
    }

    /**
     * @return ConfigurationRuleset
     */
    public function getRuleset()
    {
        return $this->ruleset;
    }


}
