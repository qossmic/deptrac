<?php

declare(strict_types=1);

namespace Internal\Qossmic\Deptrac;

use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IgnoreDependenciesOnContract implements EventSubscriberInterface
{
    public function onProcessEvent(ProcessEvent $event): void
    {
        if (array_key_exists('Contract', $event->dependentLayers)) {
            $event->stopPropagation();
        }
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ProcessEvent::class => 'onProcessEvent',
        ];
    }
}
