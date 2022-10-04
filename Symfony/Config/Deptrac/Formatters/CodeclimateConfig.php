<?php

namespace Symfony\Config\Deptrac\Formatters;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Codeclimate'.\DIRECTORY_SEPARATOR.'SeverityConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class CodeclimateConfig 
{
    private $severity;
    private $_usedProperties = [];

    /**
     * Map how failures, skipped and uncovered dependencies map to severity in CodeClimate
     * @default {"failure":"major","skipped":"minor","uncovered":"info"}
    */
    public function severity(array $value = []): \Symfony\Config\Deptrac\Formatters\Codeclimate\SeverityConfig
    {
        if (null === $this->severity) {
            $this->_usedProperties['severity'] = true;
            $this->severity = new \Symfony\Config\Deptrac\Formatters\Codeclimate\SeverityConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "severity()" has already been initialized. You cannot pass values the second time you call severity().');
        }

        return $this->severity;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('severity', $value)) {
            $this->_usedProperties['severity'] = true;
            $this->severity = new \Symfony\Config\Deptrac\Formatters\Codeclimate\SeverityConfig($value['severity']);
            unset($value['severity']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['severity'])) {
            $output['severity'] = $this->severity->toArray();
        }

        return $output;
    }

}
