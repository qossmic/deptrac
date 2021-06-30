<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

use InvalidArgumentException;

final class ConfigurationRuleset
{
    /** @var array<string, string[]> */
    private array $layerMap;

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
        return array_values(array_unique($this->getTransitiveDependencies($layerName, [])));
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
        $dependencies = [];
        foreach ($this->layerMap[$layerName] ?? [] as $layer) {
            if (0 === strncmp($layer, '+', 1)) {
                $layer = ltrim($layer, '+');
                $dependencies[] = $this->getTransitiveDependencies($layer, array_merge([$layerName], $previousLayers));
            }
            $dependencies[] = [$layer];
        }

        return [] === $dependencies ? [] : array_merge(...$dependencies);
    }
}
