<?php

namespace SensioLabs\Deptrac\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationLayer
{
    /** @var ConfigurationCollector[] */
    private $collectors;

    private $name;

    private $layers;

    /** @var ConfigurationLayer | null */
    private $parent = null;

    public static function fromArray(array $arr, $parent = null)
    {
        $options = (new OptionsResolver())->setRequired([
            'name',
            'collectors'
        ])->setDefaults([
            'layers' => []
        ])->resolve($arr);

        $self = new static(
            array_map(function ($v) {
                return ConfigurationCollector::fromArray($v);
            }, $options['collectors']),
            $options['name'],
            $parent
        );

        $self->setLayers(
            array_map(function ($v) use ($self) {
                return ConfigurationLayer::fromArray(
                    $v,
                    $self
                );
            }, $options['layers'])
        );

        return $self;
    }

    /**
     * @param $collectors
     * @param $name
     * @param ConfigurationLayer|null $parent
     * @param array $layers
     */
    private function __construct($collectors, $name, ConfigurationLayer $parent = null, $layers = [])
    {
        $this->collectors = $collectors;
        $this->name = $name;
        $this->layers = $layers;
        $this->parent = $parent;
    }

    /**
     * @return ConfigurationCollector[]
     */
    public function getCollectors()
    {
        return $this->collectors;
    }

    private function walkParents($callable) {
        if (!$this->parent) {
            return [];
        }

        return array_merge(
            [$callable($this->parent)],
            $this->parent->walkParents($callable)
        );
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPathname()
    {
        $parentNames = $this->walkParents(function(ConfigurationLayer $layer) {
           return $layer->getName();
        });

        return trim(implode(' -> ', $parentNames).' -> '.$this->name, ' -> ');
    }

    /**
     * @return ConfigurationLayer[]
     */
    public function getLayers()
    {
        return $this->layers;
    }

    /**
     * @param array $layers
     */
    public function setLayers($layers)
    {
        $this->layers = $layers;
    }

}
