<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser\EventHandler;

use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use Qossmic\Deptrac\Contract\Result\Error;
use Qossmic\Deptrac\Core\Layer\Exception\CircularReferenceException;

use function in_array;

/**
 * @internal
 */
class DependsOnDisallowedLayer extends ViolationHandler
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

        try {
            $allowedLayers = $this->layerProvider->getAllowedLayers($event->dependerLayer);
        } catch (CircularReferenceException $circularReferenceException) {
            $ruleset->addError(new Error($circularReferenceException->getMessage()));
            $event->stopPropagation();

            return;
        }

        foreach ($event->dependentLayers as $dependentLayer => $_) {
            if (!in_array($dependentLayer, $allowedLayers, true)) {
                $this->addSkippableViolation($event, $ruleset, $dependentLayer);
                $event->stopPropagation();
            }
        }
    }
}
