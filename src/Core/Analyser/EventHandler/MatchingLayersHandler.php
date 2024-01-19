<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Analyser\EventHandler;

use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use DEPTRAC_202401\Symfony\Component\EventDispatcher\EventSubscriberInterface;
/**
 * @internal
 */
class MatchingLayersHandler implements EventSubscriberInterface
{
    public function invoke(ProcessEvent $event) : void
    {
        foreach ($event->dependentLayers as $dependeeLayer => $_) {
            if ($event->dependerLayer !== $dependeeLayer) {
                return;
            }
        }
        // For empty dependee layers see UncoveredDependeeHandler
        $event->stopPropagation();
    }
    public static function getSubscribedEvents()
    {
        return [ProcessEvent::class => ['invoke', 1]];
    }
}
