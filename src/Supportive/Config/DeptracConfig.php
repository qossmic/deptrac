<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Config;

use Qossmic\Deptrac\Supportive\DependencyInjection\EmitterType;
use Symfony\Component\Config\Builder\ConfigBuilderInterface;

final class DeptracConfig implements ConfigBuilderInterface
{
    private bool $ignoreUncoveredInternalClasses = false;
    private bool $useRelativePathFromDepfile = false;
    private array $paths = ['src'];

    private array $layers = [];
    private array $formatters = [];
    private array $rulesets = [];
    private array $analyser = [];

    public function analyser(EmitterType ...$types): self
    {
        foreach ($types as $type) {
            $this->analyser[] = $type->value;
        }

        return $this;
    }

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
        return [
            'paths' => $this->paths,
            'exclude_files' => [],
            'layers' => array_map(static fn (LayerConfig $layerConfig) => $layerConfig->toArray(), $this->layers),
            'ruleset' => array_map(static fn (RulesetConfig $rulesetConfig) => $rulesetConfig->toArray(), $this->rulesets),
            'skip_violations' => [],
            'formatters' => $this->formatters,
            'analyser' => ['types' => $this->analyser],
            'ignore_uncovered_internal_classes' => $this->ignoreUncoveredInternalClasses,
            'use_relative_path_from_depfile' => $this->useRelativePathFromDepfile,
        ];
    }

    public function getExtensionAlias(): string
    {
        return 'deptrac';
    }
}
