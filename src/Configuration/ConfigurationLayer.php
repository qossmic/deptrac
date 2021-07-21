<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConfigurationLayer
{
    /** @var ConfigurationCollector[] */
    private array $collectors;

    private string $name;

    /**
     * @param array{name: string, collectors: array<array<string, string>>} $args
     */
    public static function fromArray(array $args): self
    {
        /** @var array{name: string, collectors: array<array<string, string>>} $options */
        $options = (new OptionsResolver())->setRequired([
            'name',
            'collectors',
        ])->resolve($args);

        return new self(
            array_map([self::class, 'createCollector'], $options['collectors']),
            $options['name']
        );
    }

    /**
     * @param ConfigurationCollector[] $collectors
     */
    private function __construct(array $collectors, string $name)
    {
        $this->collectors = $collectors;
        $this->name = $name;
    }

    /**
     * @param array<string, string> $args
     */
    private static function createCollector(array $args): ConfigurationCollector
    {
        return ConfigurationCollector::fromArray($args);
    }

    /**
     * @return ConfigurationCollector[]
     */
    public function getCollectors(): array
    {
        return $this->collectors;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
