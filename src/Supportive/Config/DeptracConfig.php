<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Config;

use Qossmic\Deptrac\Supportive\DependencyInjection\EmitterType;
use Symfony\Component\Config\Builder\ConfigBuilderInterface;
use Symfony\Component\Yaml\Yaml;

final class DeptracConfig implements ConfigBuilderInterface
{
    private bool $ignoreUncoveredInternalClasses = true;
    private bool $useRelativePathFromDepfile = true;

    /** @var array<string> */
    private array $paths = [];
    /** @var array<LayerConfig> */
    private array $layers = [];
    /** @var array<FormatterConfig> */
    private array $formatters = [];
    /** @var array<RulesetConfig> */
    private array $rulesets = [];
    /** @var array<string, EmitterType> */
    private array $analyser = [];
    /** @var array<string, array<string>> */
    private array $skipViolations = [];
    /** @var array<string> */
    private array $excludeFiles = [];

    public function analyser(EmitterType ...$types): self
    {
        foreach ($types as $type) {
            $this->analyser[$type->value] = $type;
        }

        return $this;
    }

    public function baseline(string $baseline): self
    {
        /** @var array<string, array<string, array<string>>> $baselineAsArray */
        $baselineAsArray = Yaml::parseFile($baseline);
        /** @var array<string, string> */
        $skipViolations = $baselineAsArray['deptrac']['skip_violations'] ?? [];

        foreach ($skipViolations as $class => $skipViolation) {
            $this->skipViolations[$class][] = $skipViolation;
        }

        return $this;
    }

    public function paths(string ...$paths): self
    {
        $this->paths = $paths;

        return $this;
    }

    public function layer(string $name): LayerConfig
    {
        return $this->layers[$name] = new LayerConfig($name);
    }

    public function ruleset(LayerConfig $layerConfig): RulesetConfig
    {
        return $this->rulesets[] = new RulesetConfig($layerConfig);
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $config = [];

        if ([] !== $this->paths) {
            $config['paths'] = $this->paths;
        }

        if ([] !== $this->excludeFiles) {
            $config['exclude_files'] = $this->excludeFiles;
        }

        if ([] !== $this->layers) {
            $config['layers'] = array_map(static fn (LayerConfig $layerConfig) => $layerConfig->toArray(), $this->layers);
        }

        if ([] !== $this->rulesets) {
            $config['ruleset'] = array_map(static fn (RulesetConfig $rulesetConfig) => $rulesetConfig->toArray(), $this->rulesets);
        }

        if ([] !== $this->skipViolations) {
            $config['skip_violations'] = $this->skipViolations;
        }

        if ([] !== $this->formatters) {
            $config['formatters'] = array_map(static fn (FormatterConfig $formatterConfig) => $formatterConfig->__toString(), $this->formatters);
        }

        if ([] !== $this->skipViolations) {
            $config['analyser']['types'] = array_map(static fn (EmitterType $emitterType) => $emitterType->value, $this->analyser);
        }

        $config['ignore_uncovered_internal_classes'] = $this->ignoreUncoveredInternalClasses;
        $config['use_relative_path_from_depfile'] = $this->useRelativePathFromDepfile;

        return $config;
    }

    public function getExtensionAlias(): string
    {
        return 'deptrac';
    }
}
