<?php

namespace SensioLabs\Deptrac\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationLayer
{
    /** @var ConfigurationCollector[] */
    private $collectors;

    private $name;

    private $layers;

    public static function fromArray(array $arr, $parentName = null)
    {
        $options = (new OptionsResolver())->setRequired([
            'name',
            'collectors'
        ])->setDefaults([
            'layers' => []
        ])->resolve($arr);

        $name = ($parentName ? $parentName .' -> '. $options['name'] : $options['name']);

        return new static(
            array_map(function ($v) { return ConfigurationCollector::fromArray($v); }, $options['collectors']),
            $name,
            array_map(function ($v) use ($name) { return ConfigurationLayer::fromArray($v, $name);}, $options['layers'])
        );
    }

    /**
     * @param $collectors
     * @param $name
     * @param $layers
     */
    private function __construct($collectors, $name, $layers)
    {
        $this->collectors = $collectors;
        $this->name = $name;
        $this->layers = $layers;
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

}
