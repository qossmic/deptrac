<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Analyser\EventHandler;

use Qossmic\Deptrac\Analyser\Event\ProcessEvent;

class MatchingLayersHandler
{
    public function __invoke(ProcessEvent $event): void
    {
        $dependerLayer = $event->getDependerLayer();
        $dependentLayers = $event->getDependentLayers();

        foreach ($dependentLayers as $dependeeLayer => $_) {
            if ($dependerLayer !== $dependeeLayer) {
                return;
            }
        }

        // For empty dependee layers see UncoveredDependeeHandler

        $event->stopPropagation();
    }
}
