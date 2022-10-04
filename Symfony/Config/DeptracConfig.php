<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Deptrac'.\DIRECTORY_SEPARATOR.'LayersConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Deptrac'.\DIRECTORY_SEPARATOR.'FormattersConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Deptrac'.\DIRECTORY_SEPARATOR.'AnalyserConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DeptracConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $paths;
    private $excludeFiles;
    private $layers;
    private $ruleset;
    private $skipViolations;
    private $formatters;
    private $analyser;
    private $ignoreUncoveredInternalClasses;
    private $useRelativePathFromDepfile;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function paths(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['paths'] = true;
        $this->paths = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function excludeFiles(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['excludeFiles'] = true;
        $this->excludeFiles = $value;

        return $this;
    }

    public function layers(string $name, array $value = []): \Symfony\Config\Deptrac\LayersConfig
    {
        if (!isset($this->layers[$name])) {
            $this->_usedProperties['layers'] = true;
            $this->layers[$name] = new \Symfony\Config\Deptrac\LayersConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "layers()" has already been initialized. You cannot pass values the second time you call layers().');
        }

        return $this->layers[$name];
    }

    /**
     * @return $this
     */
    public function ruleset(string $name, ParamConfigurator|array $value): static
    {
        $this->_usedProperties['ruleset'] = true;
        $this->ruleset[$name] = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function skipViolation(string $name, ParamConfigurator|array $value): static
    {
        $this->_usedProperties['skipViolations'] = true;
        $this->skipViolations[$name] = $value;

        return $this;
    }

    /**
     * @default {"graphviz":{"hidden_layers":[],"groups":[],"point_to_groups":false},"codeclimate":{"severity":{"failure":"major","skipped":"minor","uncovered":"info"}}}
    */
    public function formatters(array $value = []): \Symfony\Config\Deptrac\FormattersConfig
    {
        if (null === $this->formatters) {
            $this->_usedProperties['formatters'] = true;
            $this->formatters = new \Symfony\Config\Deptrac\FormattersConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "formatters()" has already been initialized. You cannot pass values the second time you call formatters().');
        }

        return $this->formatters;
    }

    /**
     * @default {"types":["class","use"]}
    */
    public function analyser(array $value = []): \Symfony\Config\Deptrac\AnalyserConfig
    {
        if (null === $this->analyser) {
            $this->_usedProperties['analyser'] = true;
            $this->analyser = new \Symfony\Config\Deptrac\AnalyserConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "analyser()" has already been initialized. You cannot pass values the second time you call analyser().');
        }

        return $this->analyser;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function ignoreUncoveredInternalClasses($value): static
    {
        $this->_usedProperties['ignoreUncoveredInternalClasses'] = true;
        $this->ignoreUncoveredInternalClasses = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function useRelativePathFromDepfile($value): static
    {
        $this->_usedProperties['useRelativePathFromDepfile'] = true;
        $this->useRelativePathFromDepfile = $value;

        return $this;
    }

    public function getExtensionAlias(): string
    {
        return 'deptrac';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('paths', $value)) {
            $this->_usedProperties['paths'] = true;
            $this->paths = $value['paths'];
            unset($value['paths']);
        }

        if (array_key_exists('exclude_files', $value)) {
            $this->_usedProperties['excludeFiles'] = true;
            $this->excludeFiles = $value['exclude_files'];
            unset($value['exclude_files']);
        }

        if (array_key_exists('layers', $value)) {
            $this->_usedProperties['layers'] = true;
            $this->layers = array_map(function ($v) { return new \Symfony\Config\Deptrac\LayersConfig($v); }, $value['layers']);
            unset($value['layers']);
        }

        if (array_key_exists('ruleset', $value)) {
            $this->_usedProperties['ruleset'] = true;
            $this->ruleset = $value['ruleset'];
            unset($value['ruleset']);
        }

        if (array_key_exists('skip_violations', $value)) {
            $this->_usedProperties['skipViolations'] = true;
            $this->skipViolations = $value['skip_violations'];
            unset($value['skip_violations']);
        }

        if (array_key_exists('formatters', $value)) {
            $this->_usedProperties['formatters'] = true;
            $this->formatters = new \Symfony\Config\Deptrac\FormattersConfig($value['formatters']);
            unset($value['formatters']);
        }

        if (array_key_exists('analyser', $value)) {
            $this->_usedProperties['analyser'] = true;
            $this->analyser = new \Symfony\Config\Deptrac\AnalyserConfig($value['analyser']);
            unset($value['analyser']);
        }

        if (array_key_exists('ignore_uncovered_internal_classes', $value)) {
            $this->_usedProperties['ignoreUncoveredInternalClasses'] = true;
            $this->ignoreUncoveredInternalClasses = $value['ignore_uncovered_internal_classes'];
            unset($value['ignore_uncovered_internal_classes']);
        }

        if (array_key_exists('use_relative_path_from_depfile', $value)) {
            $this->_usedProperties['useRelativePathFromDepfile'] = true;
            $this->useRelativePathFromDepfile = $value['use_relative_path_from_depfile'];
            unset($value['use_relative_path_from_depfile']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['paths'])) {
            $output['paths'] = $this->paths;
        }
        if (isset($this->_usedProperties['excludeFiles'])) {
            $output['exclude_files'] = $this->excludeFiles;
        }
        if (isset($this->_usedProperties['layers'])) {
            $output['layers'] = array_map(function ($v) { return $v->toArray(); }, $this->layers);
        }
        if (isset($this->_usedProperties['ruleset'])) {
            $output['ruleset'] = $this->ruleset;
        }
        if (isset($this->_usedProperties['skipViolations'])) {
            $output['skip_violations'] = $this->skipViolations;
        }
        if (isset($this->_usedProperties['formatters'])) {
            $output['formatters'] = $this->formatters->toArray();
        }
        if (isset($this->_usedProperties['analyser'])) {
            $output['analyser'] = $this->analyser->toArray();
        }
        if (isset($this->_usedProperties['ignoreUncoveredInternalClasses'])) {
            $output['ignore_uncovered_internal_classes'] = $this->ignoreUncoveredInternalClasses;
        }
        if (isset($this->_usedProperties['useRelativePathFromDepfile'])) {
            $output['use_relative_path_from_depfile'] = $this->useRelativePathFromDepfile;
        }

        return $output;
    }

}
