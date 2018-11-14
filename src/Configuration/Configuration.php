<?php

namespace SensioLabs\Deptrac\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration
{
    private $layers;
    private $paths;
    private $excludeFiles;
    private $ruleset;

    public static function fromArray(array $arr): self
    {
        $options = (new OptionsResolver())->setRequired([
            'layers',
            'paths',
            'ruleset',
        ])
        ->setDefault('exclude_files', [])
        ->addAllowedTypes('layers', 'array')
        ->addAllowedTypes('paths', 'array')
        ->addAllowedTypes('exclude_files', ['array', 'null'])
        ->addAllowedTypes('ruleset', 'array')
        ->resolve($arr);

        return new static(
            array_map(function ($v) {
                return ConfigurationLayer::fromArray($v);
            }, $options['layers']),
            ConfigurationRuleset::fromArray($options['ruleset']),
            $options['paths'],
            (array) $options['exclude_files']
        );
    }

    /**
     * @param ConfigurationLayer[] $layers
     * @param string[]             $paths
     * @param string[]             $excludeFiles
     */
    private function __construct(array $layers, ConfigurationRuleset $ruleset, array $paths, array $excludeFiles = [])
    {
        $this->layers = $layers;
        $this->ruleset = $ruleset;
        $this->paths = $paths;
        $this->excludeFiles = $excludeFiles;
    }

    /**
     * @return ConfigurationLayer[]
     */
    public function getLayers(): array
    {
        return $this->layers;
    }

    /**
     * @return string[]
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * @return string[]
     */
    public function getExcludeFiles(): array
    {
        return $this->excludeFiles;
    }

    public function getRuleset(): ConfigurationRuleset
    {
        return $this->ruleset;
    }
}
