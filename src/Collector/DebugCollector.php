<?php

namespace DependencyTracker\Collector;

use DependencyTracker\Configuration\ConfigurationLayer;
use DependencyTracker\Event\Visitor\FoundDependencyEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DebugCollector implements CollectorInterface
{
    protected $eventDispatcher;

    protected $layerConfigurtion;

    public function getType()
    {
        return 'debug';
    }

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ConfigurationLayer $layer,
        array $args
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->layerConfigurtion = $layer;
        $eventDispatcher->addListener(FoundDependencyEvent::class, [$this, 'onFoundDepdendencyEvent']);
    }

    public function onFoundDepdendencyEvent(FoundDependencyEvent $dependencyEvent)
    {
        echo $dependencyEvent->getClassA().'::'.$dependencyEvent->getClassALine().' depdends on '.$dependencyEvent->getClassB()."\n";
    }

}
