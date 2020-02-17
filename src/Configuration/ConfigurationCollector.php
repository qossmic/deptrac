<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Configuration;

final class ConfigurationCollector
{
    private $type;

    /** @var array<string, string> */
    private $args;

    /**
     * @param array<string, string> $args
     */
    public static function fromArray(array $args): self
    {
        if (!isset($args['type'])) {
            throw new \InvalidArgumentException('Collector needs a type.');
        }

        return new static($args['type'], $args);
    }

    /**
     * @param array<string, string> $args
     */
    private function __construct(string $type, array $args)
    {
        $this->type = $type;
        $this->args = $args;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array<string, string> $args
     */
    public function getArgs(): array
    {
        return $this->args;
    }
}
