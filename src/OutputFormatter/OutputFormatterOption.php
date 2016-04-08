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
    private function __construct($name, $mode = null, $description = '', $default = null)
    {
        $this->name = $name;
        $this->mode = $mode;
        $this->description = $description;
        $this->default = $default;
    }

    public static function newValueOption($name, $description, $default = null)
    {
        return new self($name, InputOption::VALUE_OPTIONAL, $description, $default);
    }

    /** @return string */
    public function getName()
    {
        return $this->name;
    }

    /** @return int|null */
    public function getMode()
    {
        return $this->mode;
    }

    /** @return string */
    public function getDescription()
    {
        return $this->description;
    }

    /** @return mixed */
    public function getDefault()
    {
        return $this->default;
    }
}
