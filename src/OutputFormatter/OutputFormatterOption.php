<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use Symfony\Component\Console\Input\InputOption;

class OutputFormatterOption
{
    public const NONE = InputOption::VALUE_NONE;
    public const REQUIRED = InputOption::VALUE_REQUIRED;
    public const OPTIONAL = InputOption::VALUE_OPTIONAL;

    /** @var string */
    private $name;
    /** @var int|null */
    private $mode;
    /** @var string */
    private $description;
    /** @var array<array-key, string>|bool|int|null|string */
    private $default;

    /**
     * @param string                                        $name        The argument name
     * @param int|null                                      $mode        The argument mode: self::REQUIRED or self::OPTIONAL
     * @param string                                        $description A description text
     * @param array<array-key, string>|bool|int|null|string $default     The default value (for self::OPTIONAL mode only)
     */
    private function __construct(string $name, int $mode = null, string $description = '', $default = null)
    {
        $this->name = $name;
        $this->mode = $mode;
        $this->description = $description;
        $this->default = $default;
    }

    /**
     * @param array<array-key, string>|bool|int|null|string $default
     */
    public static function newValueOption(string $name, string $description, $default = null, int $mode = self::OPTIONAL): self
    {
        return new self($name, $mode, $description, $default);
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

    /**
     * @return array<array-key, string>|bool|int|null|string
     */
    public function getDefault()
    {
        return $this->default;
    }
}
