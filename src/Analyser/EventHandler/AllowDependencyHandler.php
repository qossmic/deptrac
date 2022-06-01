<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Analyser\EventHandler;

use Qossmic\Deptrac\Analyser\Event\ProcessEvent;
use Qossmic\Deptrac\Layer\Exception\CircularReferenceException;
use Qossmic\Deptrac\Layer\LayerProvider;
use Qossmic\Deptrac\Result\Allowed;
use Qossmic\Deptrac\Result\Error;
use function in_array;

class AllowDependencyHandler
{
    private LayerProvider $layerProvider;

    public function __construct(LayerProvider $layerProvider)
    {
        $this->layerProvider = $layerProvider;
    }

    public function __invoke(ProcessEvent $event): void
    {
        $dependency = $event->getDependency();
        $dependerLayer = $event->getDependerLayer();
        $ruleset = $event->getResult();

        foreach ($event->getDependentLayers() as $dependentLayer) {
            try {
                $allowedLayers = $this->layerProvider->getAllowedLayers($dependerLayer);
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
}
