<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Analyser\EventHandler;

use Qossmic\Deptrac\Analyser\Event\ProcessEvent;
use Qossmic\Deptrac\Layer\Exception\CircularReferenceException;
use Qossmic\Deptrac\Result\Allowed;
use Qossmic\Deptrac\Result\Error;
use function array_merge;
use function array_unique;
use function array_values;
use function in_array;
use function ltrim;
use function strncmp;

class AllowDependencyHandler
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

    public function __invoke(ProcessEvent $event): void
    {
        $dependency = $event->getDependency();
        $dependerLayer = $event->getDependerLayer();
        $ruleset = $event->getResult();

        foreach ($event->getDependentLayers() as $dependentLayer) {
            try {
                $allowedLayers = $this->getAllowedLayers($dependerLayer);
            } catch (CircularReferenceException $circularReferenceException) {
                $ruleset->addError(new Error($circularReferenceException->getMessage()));
                $event->stopPropagation();

                return;
            }

            if (!in_array($dependentLayer, $allowedLayers, true)) {
                return;
            }

            $ruleset->add(new Allowed($dependency, $dependerLayer, $dependentLayer));

            $event->stopPropagation();
        }
    }

    /**
     * @return string[]
     */
    private function getAllowedLayers(string $layerName): array
    {
        return array_values(array_unique($this->getTransitiveDependencies($layerName, [])));
    }

    /**
     * @param string[] $previousLayers
     *
     * @return string[]
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
