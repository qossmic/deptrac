<?php

declare(strict_types=1);

use Qossmic\Deptrac\Events\Analyser\ProcessEvent;

class IgnoreDependenciesOnUtils
{
    public function __invoke(ProcessEvent $event): void
    {
        if (array_key_exists('Utils', $event->getDependentLayers())) {
            $event->stopPropagation();
        }
    }
}
