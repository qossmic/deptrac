<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser\EventHandler;

use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;

/**
 * @internal
 */
class DependsOnPrivateLayer extends ViolationHandler
{
    public static function getSubscribedEvents()
    {
        return [
            ProcessEvent::class => ['invoke', -4],
        ];
    }

    public function invoke(ProcessEvent $event): void
    {
        $ruleset = $event->getResult();

        foreach ($event->dependentLayers as $dependentLayer => $isPublic) {
            if ($event->dependerLayer === $dependentLayer && !$isPublic) {
                $this->addSkippableViolation($event, $ruleset, $dependentLayer);
                $event->stopPropagation();
            }
        }
    }
}
