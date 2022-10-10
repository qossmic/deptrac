<?php

namespace Symfony\Config\Deptrac\Formatters\Codeclimate;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class SeverityConfig 
{
    private $failure;
    private $skipped;
    private $uncovered;
    private $_usedProperties = [];

    /**
     * @default 'major'
     * @param ParamConfigurator|'info'|'minor'|'major'|'critical'|'blocker' $value
     * @return $this
     */
    public function failure($value): static
    {
        $this->_usedProperties['failure'] = true;
        $this->failure = $value;

        return $this;
    }

    /**
     * @default 'minor'
     * @param ParamConfigurator|'info'|'minor'|'major'|'critical'|'blocker' $value
     * @return $this
     */
    public function skipped($value): static
    {
        $this->_usedProperties['skipped'] = true;
        $this->skipped = $value;

        return $this;
    }

    /**
     * @default 'info'
     * @param ParamConfigurator|'info'|'minor'|'major'|'critical'|'blocker' $value
     * @return $this
     */
    public function uncovered($value): static
    {
        $this->_usedProperties['uncovered'] = true;
        $this->uncovered = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('failure', $value)) {
            $this->_usedProperties['failure'] = true;
            $this->failure = $value['failure'];
            unset($value['failure']);
        }

        if (array_key_exists('skipped', $value)) {
            $this->_usedProperties['skipped'] = true;
            $this->skipped = $value['skipped'];
            unset($value['skipped']);
        }

        if (array_key_exists('uncovered', $value)) {
            $this->_usedProperties['uncovered'] = true;
            $this->uncovered = $value['uncovered'];
            unset($value['uncovered']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['failure'])) {
            $output['failure'] = $this->failure;
        }
        if (isset($this->_usedProperties['skipped'])) {
            $output['skipped'] = $this->skipped;
        }
        if (isset($this->_usedProperties['uncovered'])) {
            $output['uncovered'] = $this->uncovered;
        }

        return $output;
    }

}
