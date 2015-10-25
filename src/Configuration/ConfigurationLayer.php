<?php

namespace DependencyTracker\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationLayer
{

    private $color;

    /** @var ConfigurationCollector[] */
    private $collectors;

    private $name;

    public static function fromArray(array $arr)
    {
        $options = (new OptionsResolver())->setRequired([
            'name',
            'color',
            'collectors'
        ])->resolve($arr);

        return new static(
            $options['color'],
            array_map(function($v) { return ConfigurationCollector::fromArray($v); }, $options['collectors']),
            $options['name']
        );
    }

    /**
     * ConfigurationLayer constructor.
     * @param $color
     * @param $collectors
     */
    public function __construct($color, $collectors, $name)
    {
        $this->color = $color;
        $this->collectors = $collectors;
        $this->name = $name;
    }


    /**
     * @return ConfigurationCollector[]
     */
    public function getCollectors()
    {
        return $this->collectors;
    }


    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

}
