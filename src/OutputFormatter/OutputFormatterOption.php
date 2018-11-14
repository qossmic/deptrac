<?php

namespace SensioLabs\Deptrac\OutputFormatter;

use Symfony\Component\Console\Input\InputOption;

class OutputFormatterOption
{
    private $name;
    private $mode;
    private $description;
    private $default;

    /**
     * @param string $name        The argument name
     * @param int    $mode        The argument mode: self::REQUIRED or self::OPTIONAL
     * @param string $description A description text
     * @param mixed  $default     The default value (for self::OPTIONAL mode only)
     */
    private function __construct(string $name, int $mode = null, string $description = '', $default = null)
    {
        $this->name = $name;
        $this->mode = $mode;
        $this->description = $description;
        $this->default = $default;
    }

    public static function newValueOption(string $name, string $description, $default = null): self
    {
        return new self($name, InputOption::VALUE_OPTIONAL, $description, $default);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMode(): ?int
    {
        return $this->mode;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDefault()
    {
        return $this->default;
    }
}
