<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use InvalidArgumentException;

class OutputFormatterInput
{
    /**
     * @var mixed[]
     */
    private array $options;

    /** @var array<string, mixed> */
    private $config;

    /**
     * @param mixed[]              $options
     * @param array<string, mixed> $config
     */
    public function __construct(array $options, array $config = [])
    {
        $this->options = $options;
        $this->config = $config;
    }

    /**
     * @throws InvalidArgumentException on not configured option
     *
     * @return mixed
     */
    public function getOption(string $name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException('option '.$name.' is not configured.');
        }

        return $this->options[$name];
    }

    public function getOptionAsBoolean(string $name): bool
    {
        return true === filter_var($this->getOption($name), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
