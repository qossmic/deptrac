<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Configuration;

class ConfigurationCollector
{
    private $type;

    private $args;

    public static function fromArray(array $arr): self
    {
        if (!isset($arr['type'])) {
            throw new \InvalidArgumentException('Collector needs a type.');
        }

        return new static($arr['type'], $arr);
    }

    private function __construct(string $type, array $args)
    {
        $this->type = $type;
        $this->args = $args;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getArgs(): array
    {
        return $this->args;
    }
}
