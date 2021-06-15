<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

use InvalidArgumentException;

final class ConfigurationRuleset
{
    /** @var array<string, string[]> */
    private $layerMap;

    /**
     * @param array<string, string[]> $arr
     */
    public static function fromArray(array $arr): self
    {
        return new self($arr);
    }

    /**
     * @param array<string, string[]> $layerMap
     */
    private function __construct(array $layerMap)
    {
        $this->layerMap = $layerMap;
    }

    /**
     * @return string[]
     *
     * @throws InvalidArgumentException
     */
    public function getAllowedDependencies(string $layerName): array
    {
        $dependencies = [];
        foreach ($this->layerMap[$layerName] ?? [] as $layer) {
            if (0 === strncmp($layer, '+', 1)) {
                $layer = ltrim($layer, '+');
                $dependencies[] = $this->getTransitiveDependencies($layer, [$layerName]);
            }
            $dependencies[] = [$layer];
        }

        return [] === $dependencies ? [] : array_unique(array_merge(...$dependencies));
    }

    /**
     * @param string[] $previousLayers
     *
     * @return string[]
     *
     * @throws InvalidArgumentException
     */
    private function getTransitiveDependencies(string $layerName, array $previousLayers): array
    {
        if (in_array($layerName, $previousLayers, true)) {
            throw new InvalidArgumentException('Circular ruleset dependency for layer '.$layerName.' depending on: '.implode('->', $previousLayers));
        }
        $transitiveDependencies = [];
        $nonTransitiveDependencies = [];
        foreach ($this->layerMap[$layerName] ?? [] as $layer) {
            if (0 === strncmp($layer, '+', 1)) {
                $layer = ltrim($layer, '+');
                $transitiveDependencies[] = $this->getTransitiveDependencies($layer, array_merge([$layerName], $previousLayers));
            }
            $nonTransitiveDependencies[] = $layer;
        }

        return array_merge($nonTransitiveDependencies, ...$transitiveDependencies);
    }
}
