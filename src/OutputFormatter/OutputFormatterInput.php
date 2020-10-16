<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\OutputFormatter;

class OutputFormatterInput
{
    /**
     * @var mixed[]
     */
    private $options;

    /**
     * @param mixed[] $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @throws \InvalidArgumentException on not configured option
     *
     * @return mixed
     */
    public function getOption(string $name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new \InvalidArgumentException('option '.$name.' is not configured.');
        }

        return $this->options[$name];
    }

    public function getOptionAsBoolean(string $name): bool
    {
        return true === filter_var($this->getOption($name), FILTER_VALIDATE_BOOLEAN);
    }
}
