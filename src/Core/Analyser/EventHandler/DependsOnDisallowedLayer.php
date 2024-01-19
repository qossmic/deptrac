<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Analyser\EventHandler;

use Qossmic\Deptrac\Contract\Analyser\EventHelper;
use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use Qossmic\Deptrac\Contract\Analyser\ViolationCreatingInterface;
use Qossmic\Deptrac\Contract\Layer\CircularReferenceException;
use Qossmic\Deptrac\Contract\Result\Error;
use function in_array;
/**
 * @internal
 */
class DependsOnDisallowedLayer implements ViolationCreatingInterface
{
    public function __construct(private readonly EventHelper $eventHelper)
    {
    }
    public static function getSubscribedEvents()
    {
        return [ProcessEvent::class => ['invoke', -1]];
    }
    public function invoke(ProcessEvent $event) : void
    {
        $ruleset = $event->getResult();
        try {
            $allowedLayers = $this->eventHelper->layerProvider->getAllowedLayers($event->dependerLayer);
        } catch (CircularReferenceException $circularReferenceException) {
            $ruleset->addError(new Error($circularReferenceException->getMessage()));
            $event->stopPropagation();
            return;
        }
        foreach ($event->dependentLayers as $dependentLayer => $_) {
            if (!in_array($dependentLayer, $allowedLayers, \true)) {
                $this->eventHelper->addSkippableViolation($event, $ruleset, $dependentLayer, $this);
                $event->stopPropagation();
            }
        }
    }
    /**
     * @psalm-pure
     */
    public function ruleName() : string
    {
        return 'DependsOnDisallowedLayer';
    }
    /**
     * @psalm-pure
     */
    public function ruleDescription() : string
    {
        return 'You are depending on token that is a part of a layer that you are not allowed to depend on.';
    }
}
