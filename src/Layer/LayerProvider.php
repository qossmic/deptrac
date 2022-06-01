<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer;

use Qossmic\Deptrac\Layer\Exception\CircularReferenceException;

class LayerProvider
{
    /**
     * @var array<string, string[]>
     */
    private array $allowedLayers;

    /**
     * @param array<string, string[]> $allowedLayers
     */
    public function __construct(array $allowedLayers)
    {
        $this->allowedLayers = $allowedLayers;
    }

    /**
     * @return string[]
     *
     * @throws CircularReferenceException
     */
    public function getAllowedLayers(string $layerName): array
    {
        return array_values(array_unique($this->getTransitiveDependencies($layerName, [])));
    }

    /**
     * @param string[] $previousLayers
     *
     * @return string[]
     *
     * @throws CircularReferenceException
     */
    private function getTransitiveDependencies(string $layerName, array $previousLayers): array
    {
        if (in_array($layerName, $previousLayers, true)) {
            throw CircularReferenceException::circularLayerDependency($layerName, $previousLayers);
        }
        $dependencies = [];
        foreach ($this->allowedLayers[$layerName] ?? [] as $layer) {
            if (0 === strncmp($layer, '+', 1)) {
                $layer = ltrim($layer, '+');
                $dependencies[] = $this->getTransitiveDependencies($layer, array_merge([$layerName], $previousLayers));
            }
            $dependencies[] = [$layer];
        }

        return [] === $dependencies ? [] : array_merge(...$dependencies);
    }
}
