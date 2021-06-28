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
     * @param array<string, mixed> $args
     */
    public static function fromArray(array $args): self
    {
        $options = (new OptionsResolver())->setRequired([
            'name',
            'collectors',
        ])->resolve($args);

        return new self(
            array_map(static function (array $v): ConfigurationCollector {
                return ConfigurationCollector::fromArray($v);
            }, $options['collectors']),
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
}
