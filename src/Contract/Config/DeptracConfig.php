<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Config;

use Qossmic\Deptrac\Contract\Config\Formatter\FormatterConfigInterface;
use Symfony\Component\Config\Builder\ConfigBuilderInterface;
use Symfony\Component\Yaml\Yaml;

final class DeptracConfig implements ConfigBuilderInterface
{
    private bool $ignoreUncoveredInternalClasses = true;
    private bool $useRelativePathFromDepfile = true;

    /** @var array<string> */
    private array $paths = [];
    /** @var array<Layer> */
    private array $layers = [];
    /** @var array<FormatterConfigInterface> */
    private array $formatters = [];
    /** @var array<RulesetConfig> */
    private array $rulesets = [];
    /** @var array<string, \Qossmic\Deptrac\Contract\Config\EmitterType> */
    private array $analyser = [];
    /** @var array<string, array<string>> */
    private array $skipViolations = [];
    /** @var array<string> */
    private array $excludeFiles = [];

    public function analysers(EmitterType ...$types): self
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

        /** @var array<string, array<string>> $skipViolations */
        $skipViolations = $baselineAsArray['deptrac']['skip_violations'] ?? [];

        foreach ($skipViolations as $class => $skipViolation) {
            $this->skipViolations[$class] = $skipViolation;
        }

        return $this;
    }

    public function formatters(FormatterConfigInterface ...$formatters): self
    {
        foreach ($formatters as $formatter) {
            $this->formatters[$formatter->getName()] = $formatter;
        }

        return $this;
    }

    public function paths(string ...$paths): self
    {
        foreach ($paths as $path) {
            $this->paths[] = $path;
        }

        return $this;
    }

    public function excludeFiles(string ...$excludeFiles): self
    {
        foreach ($excludeFiles as $excludeFile) {
            $this->excludeFiles[] = $excludeFile;
        }

        return $this;
    }

    public function layers(Layer ...$layerConfigs): self
    {
        foreach ($layerConfigs as $layerConfig) {
            $this->layers[$layerConfig->name] = $layerConfig;
        }

        return $this;
    }

    public function rulesets(RulesetConfig ...$rulesetConfigs): self
    {
        foreach ($rulesetConfigs as $rulesetConfig) {
            $this->rulesets[$rulesetConfig->layersConfig->name] = $rulesetConfig;
        }

        return $this;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $config = [];

        if ([] !== $this->paths) {
            $config['paths'] = $this->paths;
        }

        if ([] !== $this->analyser) {
            $config['analyser']['types'] = array_map(static fn (EmitterType $emitterType) => $emitterType->value, $this->analyser);
        }

        if ([] !== $this->formatters) {
            $config['formatters'] = array_map(static fn (FormatterConfigInterface $formatterConfig) => $formatterConfig->toArray(), $this->formatters);
        }

        if ([] !== $this->excludeFiles) {
            $config['exclude_files'] = $this->excludeFiles;
        }

        if ([] !== $this->layers) {
            $config['layers'] = array_map(static fn (Layer $layerConfig) => $layerConfig->toArray(), $this->layers);
        }

        if ([] !== $this->rulesets) {
            $config['ruleset'] = array_map(static fn (RulesetConfig $rulesetConfig) => $rulesetConfig->toArray(), $this->rulesets);
        }

        if ([] !== $this->skipViolations) {
            $config['skip_violations'] = $this->skipViolations;
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

const ESCAPEES = [
    '\\',
    '\\\\',
    '\\"',
    '"',
    "\x00",
    "\x01",
    "\x02",
    "\x03",
    "\x04",
    "\x05",
    "\x06",
    "\x07",
    "\x08",
    "\x09",
    "\x0a",
    "\x0b",
    "\x0c",
    "\x0d",
    "\x0e",
    "\x0f",
    "\x10",
    "\x11",
    "\x12",
    "\x13",
    "\x14",
    "\x15",
    "\x16",
    "\x17",
    "\x18",
    "\x19",
    "\x1a",
    "\x1b",
    "\x1c",
    "\x1d",
    "\x1e",
    "\x1f",
    "\x7f",
    "\xc2\x85",
    "\xc2\xa0",
    "\xe2\x80\xa8",
    "\xe2\x80\xa9",
];

const ESCAPED = [
    '\\\\',
    '\\"',
    '\\\\',
    '\\"',
    '\\0',
    '\\x01',
    '\\x02',
    '\\x03',
    '\\x04',
    '\\x05',
    '\\x06',
    '\\a',
    '\\b',
    '\\t',
    '\\n',
    '\\v',
    '\\f',
    '\\r',
    '\\x0e',
    '\\x0f',
    '\\x10',
    '\\x11',
    '\\x12',
    '\\x13',
    '\\x14',
    '\\x15',
    '\\x16',
    '\\x17',
    '\\x18',
    '\\x19',
    '\\x1a',
    '\\e',
    '\\x1c',
    '\\x1d',
    '\\x1e',
    '\\x1f',
    '\\x7f',
    '\\N',
    '\\_',
    '\\L',
    '\\P',
];

function regex(string $regex): string
{
    return sprintf('%s', str_replace(ESCAPEES, ESCAPED, $regex));
}
