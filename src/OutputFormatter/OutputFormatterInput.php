<?php

namespace SensioLabs\Deptrac\OutputFormatter;

class OutputFormatterInput
{
    private $options;

    public function __construct(array $arguments)
    {
        $this->options = $arguments;
    }

    public function getOption(string $name)
    {
        if (!isset($this->options[$name])) {
            throw new \InvalidArgumentException('option '.$name.' is not configured.');
        }

        return $this->options[$name];
    }
}
