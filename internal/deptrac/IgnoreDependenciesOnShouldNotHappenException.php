<?php

declare(strict_types=1);

namespace Internal\Qossmic\Deptrac;

use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;

class IgnoreDependenciesOnShouldNotHappenException
{
    public function __invoke(ProcessEvent $event): void
    {
        if ("Qossmic\Deptrac\Supportive\ShouldNotHappenException" === $event->getDependentReference()->getToken()->toString()) {
            $event->stopPropagation();
        }
    }
}
