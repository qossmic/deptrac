<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration
{
    private $layers;
    private $paths;
    private $excludeFiles;
    private $ruleset;
    private $skipViolations;

    public static function fromArray(array $arr): self
    {
        $options = (new OptionsResolver())->setRequired([
            'layers',
            'paths',
            'ruleset',
        ])
        ->setDefault('exclude_files', [])
        ->setDefault('skip_violations', [])
        ->addAllowedTypes('layers', 'array')
        ->addAllowedTypes('paths', 'array')
        ->addAllowedTypes('exclude_files', ['array', 'null'])
        ->addAllowedTypes('ruleset', 'array')
        ->addAllowedTypes('skip_violations', 'array')
        ->resolve($arr);

        return new static(
            array_map(function ($v) {
                return ConfigurationLayer::fromArray($v);
            }, $options['layers']),
            ConfigurationRuleset::fromArray($options['ruleset']),
            $options['paths'],
            ConfigurationSkippedViolation::fromArray($options['skip_violations']),
            (array) $options['exclude_files']
        );
    }

    /**
     * @param ConfigurationLayer[] $layers
     * @param string[]             $paths
     * @param string[]             $excludeFiles
     */
    private function __construct(array $layers, ConfigurationRuleset $ruleset, array $paths, ConfigurationSkippedViolation $skipViolations, array $excludeFiles = [])
    {
        $this->layers = $layers;
        $this->ruleset = $ruleset;
        $this->paths = $paths;
        $this->excludeFiles = $excludeFiles;
        $this->skipViolations = $skipViolations;
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

    public function getSkipViolations(): ConfigurationSkippedViolation
    {
        return $this->skipViolations;
    }
}
