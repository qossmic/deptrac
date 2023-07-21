<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser\EventHandler;

use Qossmic\Deptrac\Contract\Analyser\EventHelper;
use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use Qossmic\Deptrac\Contract\Analyser\ViolationCreatingInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;

/**
 * @internal
 */
class DependsOnInternalToken implements ViolationCreatingInterface
{
    public function __construct(private readonly EventHelper $eventHelper) {}

    public static function getSubscribedEvents()
    {
        return [
            ProcessEvent::class => ['invoke', -2],
        ];
    }

    public function invoke(ProcessEvent $event): void
    {
        $ruleset = $event->getResult();
        foreach ($event->dependentLayers as $dependentLayer => $_) {
            if ($event->dependerLayer !== $dependentLayer
                && $event->dependentReference instanceof ClassLikeReference
                && $event->dependentReference->isInternal
            ) {
                $this->eventHelper->addSkippableViolation($event, $ruleset, $dependentLayer, $this);
                $event->stopPropagation();
            }
        }
    }

    /**
     * @psalm-pure
     */
    public function ruleName(): string
    {
        return 'DependsOnInternalToken';
    }

    /**
     * @psalm-pure
     */
    public function ruleDescription(): string
    {
        return 'You are depending on a token that is internal to the layer and you are not part of that layer.';
    }
}
