<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser\EventHandler;

use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;

/**
 * @internal
 */
class DependsOnInternalToken extends ViolationHandler
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
        foreach ($event->dependentLayers as $dependentLayer => $_) {
            if ($event->dependerLayer !== $dependentLayer
                && $event->dependentReference instanceof ClassLikeReference
                && $event->dependentReference->isInternal
            ) {
                $this->addSkippableViolation($event, $ruleset, $dependentLayer);
                $event->stopPropagation();
            }
        }
    }
}
