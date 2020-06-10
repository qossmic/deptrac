<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration
{
    /** @var ConfigurationLayer[] */
    private $layers;
    /** @var string[] */
    private $paths;
    /** @var string[] */
    private $excludeFiles;
    /** @var ConfigurationRuleset */
    private $ruleset;
    /** @var ConfigurationSkippedViolation */
    private $skipViolations;
    /** @var bool */
    private $ignoreUncoveredInternalClasses;

    /**
     * @param array<string, mixed> $args
     */
    public static function fromArray(array $args): self
    {
        $options = (new OptionsResolver())->setRequired([
            'layers',
            'paths',
            'ruleset',
        ])
        ->setDefault('exclude_files', [])
        ->setDefault('skip_violations', [])
        ->setDefault('ignore_uncovered_internal_classes', true)
        ->addAllowedTypes('layers', 'array')
        ->addAllowedTypes('paths', 'array')
        ->addAllowedTypes('exclude_files', ['array', 'null'])
        ->addAllowedTypes('ruleset', 'array')
        ->addAllowedTypes('skip_violations', 'array')
        ->addAllowedTypes('ignore_uncovered_internal_classes', 'bool')
        ->resolve($args);

        $instance = new self();
        $instance->layers = array_map(static function ($v): ConfigurationLayer {
            return ConfigurationLayer::fromArray($v);
        }, $options['layers']);
        $instance->ruleset = ConfigurationRuleset::fromArray($options['ruleset']);
        $instance->paths = $options['paths'];
        $instance->skipViolations = ConfigurationSkippedViolation::fromArray($options['skip_violations']);
        $instance->excludeFiles = (array) $options['exclude_files'];
        $instance->ignoreUncoveredInternalClasses = (bool) $options['ignore_uncovered_internal_classes'];

        return $instance;
    }

    private function __construct()
    {
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

    public function ignoreUncoveredInternalClasses(): bool
    {
        return $this->ignoreUncoveredInternalClasses;
    }
}
