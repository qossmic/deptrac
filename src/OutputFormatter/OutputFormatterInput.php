<?php

namespace SensioLabs\Deptrac\OutputFormatter;

class OutputFormatterInput
{
    private $options;

    /**
     * @param array $arguments
     */
    public function __construct($arguments)
    {
        $this->options = $arguments;
    }

    /**
     * @param string $name
     */
    public function getOption($name)
    {
        if (!isset($this->options[$name])) {
            throw new \InvalidArgumentException('option '.$name.' is not configured.');
        }

        return $this->options[$name];
    }
}
