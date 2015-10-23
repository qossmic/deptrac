<?php

namespace DependencyTracker\Collector;

use DependencyTracker\Event\Visitor\FoundDependencyEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DebugCollector
{
    protected $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        $eventDispatcher->addListener(FoundDependencyEvent::class, [$this, 'onFoundDepdendencyEvent']);
    }

    public function onFoundDepdendencyEvent(FoundDependencyEvent $dependencyEvent)
    {
        echo $dependencyEvent->getClassA().'::'.$dependencyEvent->getClassALine().' depdends on '.$dependencyEvent->getClassB()."\n";
    }

}
