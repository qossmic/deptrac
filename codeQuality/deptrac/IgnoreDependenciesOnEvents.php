<?php

declare(strict_types=1);

use Qossmic\Deptrac\Events\Analyser\ProcessEvent;

class IgnoreDependenciesOnEvents
{
    public function __invoke(ProcessEvent $event): void
    {
        if (array_key_exists('Events', $event->getDependentLayers())) {
            $event->stopPropagation();
        }
    }
}
