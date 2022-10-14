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

    private array $paths = [];
    private array $layers = [];
    private array $formatters = [];
    private array $rulesets = [];
    private array $analyser = [];
    private array $skipViolations = [];
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
        $baseline = Yaml::parseFile($baseline);

        foreach ($baseline['deptrac']['skip_violations'] ?? [] as $class => $skipViolations) {
            $this->skipViolations[$class] = $skipViolations;
        }

        return $this;
    }

    public function paths(array $paths): self
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
            $config['formatters'] = $this->formatters;
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
