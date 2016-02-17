<?php

namespace DependencyTracker;

use DependencyTracker\Configuration\ConfigurationLayer;
use DependencyTracker\Configuration\ConfigurationRuleset;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration
{

    private $layers;

    private $paths;

    private $exclude_files;

    private $ruleset;

    public static function fromArray(array $arr)
    {
        $options = (new OptionsResolver())->setRequired([
            'layers',
            'paths',
            'exclude_files',
            'ruleset'
        ])
        ->resolve($arr);

        return new static(
            array_map(function($v) { return ConfigurationLayer::fromArray($v); }, $options['layers']),
            ConfigurationRuleset::fromArray($options['ruleset']),
            $options['paths'],
            $options['exclude_files']
        );
    }

    /**
     * @param $layers
     * @param $ruleset
     * @param $paths
     * @param $exclude_files
     * @param $formatter
     */
    private function __construct($layers, $ruleset, $paths, $exclude_files)
    {
        $this->layers = $layers;
        $this->ruleset = $ruleset;
        $this->paths = $paths;
        $this->exclude_files = $exclude_files;
    }

    /**
     * @return ConfigurationLayer[]
     */
    public function getLayers()
    {
        return $this->layers;
    }

    /**
     * @return mixed
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * @return mixed
     */
    public function getExcludeFiles()
    {
        return $this->exclude_files;
    }

    /**
     * @return ConfigurationRuleset
     */
    public function getRuleset()
    {
        return $this->ruleset;
    }

}
