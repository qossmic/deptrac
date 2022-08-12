<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser\EventHandler;

use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;

/**
 * @internal
 */
class MatchingLayersHandler
{
    public function __invoke(ProcessEvent $event): void
    {
        foreach ($event->dependentLayers as $dependeeLayer => $_) {
            if ($event->dependerLayer !== $dependeeLayer) {
                return;
            }
        }

        // For empty dependee layers see UncoveredDependeeHandler

        $event->stopPropagation();
    }
}
