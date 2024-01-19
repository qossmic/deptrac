<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Contract\Config;

use Qossmic\Deptrac\Contract\Config\Formatter\FormatterConfigInterface;
use DEPTRAC_202401\Symfony\Component\Config\Builder\ConfigBuilderInterface;
use DEPTRAC_202401\Symfony\Component\Yaml\Exception\ParseException;
use DEPTRAC_202401\Symfony\Component\Yaml\Yaml;
final class DeptracConfig implements ConfigBuilderInterface
{
    private bool $ignoreUncoveredInternalClasses = \true;
    /** @var array<string> */
    private array $paths = [];
    /** @var array<Layer> */
    private array $layers = [];
    /** @var array<FormatterConfigInterface> */
    private array $formatters = [];
    /** @var array<Ruleset> */
    private array $rulesets = [];
    /** @var array<string, EmitterType> */
    private array $analyser = [];
    /** @var array<string, array<string>> */
    private array $skipViolations = [];
    /** @var array<string> */
    private array $excludeFiles = [];
    public function analysers(\Qossmic\Deptrac\Contract\Config\EmitterType ...$types) : self
    {
        foreach ($types as $type) {
            $this->analyser[$type->value] = $type;
        }
        return $this;
    }
    /**
     * @throws ParseException
     */
    public function baseline(string $baseline) : self
    {
        /** @var array<string, array<string, array<string>>> $baselineAsArray */
        $baselineAsArray = Yaml::parseFile($baseline);
        /** @var array<string, array<string>> $skipViolations */
        $skipViolations = $baselineAsArray['deptrac']['skip_violations'] ?? [];
        foreach ($skipViolations as $class => $skipViolation) {
            $this->skipViolations[$class] = $skipViolation;
        }
        return $this;
    }
    public function formatters(FormatterConfigInterface ...$formatters) : self
    {
        foreach ($formatters as $formatter) {
            $this->formatters[$formatter->getName()] = $formatter;
        }
        return $this;
    }
    public function paths(string ...$paths) : self
    {
        foreach ($paths as $path) {
            $this->paths[] = $path;
        }
        return $this;
    }
    public function excludeFiles(string ...$excludeFiles) : self
    {
        foreach ($excludeFiles as $excludeFile) {
            $this->excludeFiles[] = $excludeFile;
        }
        return $this;
    }
    public function layers(\Qossmic\Deptrac\Contract\Config\Layer ...$layerConfigs) : self
    {
        foreach ($layerConfigs as $layerConfig) {
            $this->layers[$layerConfig->name] = $layerConfig;
        }
        return $this;
    }
    public function rulesets(\Qossmic\Deptrac\Contract\Config\Ruleset ...$rulesetConfigs) : self
    {
        foreach ($rulesetConfigs as $rulesetConfig) {
            $this->rulesets[$rulesetConfig->layerConfig->name] = $rulesetConfig;
        }
        return $this;
    }
    /** @return array<mixed> */
    public function toArray() : array
    {
        $config = [];
        if ([] !== $this->paths) {
            $config['paths'] = $this->paths;
        }
        if ([] !== $this->analyser) {
            $config['analyser']['types'] = \array_map(static fn(\Qossmic\Deptrac\Contract\Config\EmitterType $emitterType) => $emitterType->value, $this->analyser);
        }
        if ([] !== $this->formatters) {
            $config['formatters'] = \array_map(static fn(FormatterConfigInterface $formatterConfig) => $formatterConfig->toArray(), $this->formatters);
        }
        if ([] !== $this->excludeFiles) {
            $config['exclude_files'] = $this->excludeFiles;
        }
        if ([] !== $this->layers) {
            $config['layers'] = \array_map(static fn(\Qossmic\Deptrac\Contract\Config\Layer $layerConfig) => $layerConfig->toArray(), $this->layers);
        }
        if ([] !== $this->rulesets) {
            $config['ruleset'] = \array_map(static fn(\Qossmic\Deptrac\Contract\Config\Ruleset $rulesetConfig) => $rulesetConfig->toArray(), $this->rulesets);
        }
        if ([] !== $this->skipViolations) {
            $config['skip_violations'] = $this->skipViolations;
        }
        $config['ignore_uncovered_internal_classes'] = $this->ignoreUncoveredInternalClasses;
        return $config;
    }
    public function getExtensionAlias() : string
    {
        return 'deptrac';
    }
}
