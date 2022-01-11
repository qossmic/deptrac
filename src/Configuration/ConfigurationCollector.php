<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

use InvalidArgumentException;

final class ConfigurationCollector
{
    private string $type;

    /** @var array{type: string, args: array<string, string>} */
    private array $args;

    /**
     * @param array{type?: string, args: array<string, string>} $args
     */
    public static function fromArray(array $args): self
    {
        if (!isset($args['type'])) {
            throw new InvalidArgumentException('Collector needs a type.');
        }

        return new self($args['type'], $args);
    }

    /**
     * @param array{type: string, args: array<string, string>} $args
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
     * @return array{type: string, args: array<string, string>}
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @return array{type: string, args: array<string, string>}
     */
    public function toArray(): array
    {
        return $this->getArgs();
    }
}
