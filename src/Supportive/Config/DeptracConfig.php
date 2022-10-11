<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Config;

use Symfony\Component\Config\Builder\ConfigBuilderInterface;

final class DeptracConfig implements ConfigBuilderInterface
{
    private ?string $baseline = null;
    private bool $ignoreUncoveredInternalClasses = false;
    private bool $useRelativePathFromDepfile = false;
    private array $paths = ['src'] ;
    private array $layers = [];
    private array $formatters = [];

    public function baseline(string $baseline): self
    {
        $this->baseline = $baseline;

        return $this;
    }

    public function paths(array $paths): self
    {
        $this->paths = $paths;

        return $this;
    }

    public function layers(string $name): LayersConfig
    {
        return $this->layers[$name] = new LayersConfig($name);
    }

    public function rulesets(string $name): RulesetConfig
    {
        return $this->ruleset[$name] = new RulesetConfig($name);
    }

    public function toArray(): array
    {
        return [
            'paths' => $this->paths,
            'exclude_files' => [],
            'layers' => array_map(fn (LayersConfig $layerConfig)=> $layerConfig->toArray(), $this->layers),
            'ruleset' => array_map(fn (RulesetConfig $rulesetConfig) => $rulesetConfig(), $this->rulesets),
            'skip_violations' => [],
            'formatters' => [],
            'analyser' => $this->analysers,
            'ignore_uncovered_internal_classes' => $this->ignoreUncoveredInternalClasses,
            'use_relative_path_from_depfile' => $this->useRelativePathFromDepfile,
        ];
    }

    public function getExtensionAlias(): string
    {
        return 'deptrac';
    }
}
