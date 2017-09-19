<?php

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\Configuration\ConfigurationLayer;
use SensioLabs\Deptrac\Configuration\ConfigurationRuleset;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration
{
    private $layers;
    private $paths;
    private $exclude_files;
    private $ruleset;

    public static function fromArray(array $arr)
    {
        $options = self::resolveOptions($arr);

        return new static(
            self::createConfigurationLayers($options['layers']),
            ConfigurationRuleset::fromArray($options['ruleset']),
            $options['paths'],
            (array)$options['exclude_files']
        );
    }

    /**
     * @param ConfigurationLayer[] $layers
     * @param ConfigurationRuleset $ruleset
     * @param array                $paths
     * @param array                $exclude_files
     */
    private function __construct(array $layers, ConfigurationRuleset $ruleset, array $paths, array $exclude_files)
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
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * @return array
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

    private static function resolveOptions(array $arr)
    {
        return (new OptionsResolver())
            ->setRequired(
                [
                    'layers',
                    'paths',
                    'ruleset',
                ]
            )
            ->setDefault('exclude_files', [])
            ->addAllowedTypes('layers', 'array')
            ->addAllowedTypes('paths', 'array')
            ->addAllowedTypes('exclude_files', ['array', 'null'])
            ->addAllowedTypes('ruleset', 'array')
            ->resolve($arr);
    }

    private static function createConfigurationLayers(array $layers)
    {
        return array_map(
            function ($v) {
                return ConfigurationLayer::fromArray($v);
            },
            $layers
        );
    }
}
