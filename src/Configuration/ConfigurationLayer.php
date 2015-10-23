<?php

namespace DependencyTracker\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationLayer
{

    private $color;

    /** @var ConfigurationCollector[] */
    private $collectors;

    public static function fromArray(array $arr)
    {
        $options = (new OptionsResolver())->setRequired([
            'color',
            'collectors'
        ])->resolve($arr);

        return new static(
            $options['color'],
            array_map(function($v) { return ConfigurationCollector::fromArray($v); }, $options['collectors'])
        );
    }

    /**
     * ConfigurationLayer constructor.
     * @param $color
     * @param $collectors
     */
    public function __construct($color, $collectors)
    {
        $this->color = $color;
        $this->collectors = $collectors;
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

}
