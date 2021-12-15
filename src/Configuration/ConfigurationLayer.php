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
            array_map([ConfigurationCollector::class, 'fromArray'], $options['collectors']),
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

    /**
     * @return array{name: string, collectors: array<array<string, string>>}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'collectors' => array_map(static function (ConfigurationCollector $collector): array {
                return $collector->toArray();
            }, $this->getCollectors())
        ];
    }
}
