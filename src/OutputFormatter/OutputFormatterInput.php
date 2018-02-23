<?php

namespace SensioLabs\Deptrac\OutputFormatter;

class OutputFormatterInput
{
    private $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function getOption(string $name)
    {
        if (!isset($this->options[$name])) {
            throw new \InvalidArgumentException('option '.$name.' is not configured.');
        }

        return $this->options[$name];
    }
}
