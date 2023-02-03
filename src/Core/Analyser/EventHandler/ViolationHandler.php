<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser\EventHandler;

use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use Qossmic\Deptrac\Contract\Result\Result;
use Qossmic\Deptrac\Contract\Result\SkippedViolation;
use Qossmic\Deptrac\Contract\Result\Violation;
use Qossmic\Deptrac\Core\Layer\LayerProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
abstract class ViolationHandler implements EventSubscriberInterface
{
    public function __construct(
        protected readonly SkippedViolationHelper $skippedViolationHelper,
        protected readonly LayerProvider $layerProvider,
    ) {
    }

    public function addSkippableViolation(ProcessEvent $event, Result $result, string $dependentLayer): void
    {
        if ($this->skippedViolationHelper->isViolationSkipped(
            $event->dependency->getDepender()
                ->toString(),
            $event->dependency->getDependent()
                ->toString()
        )
        ) {
            $result->add(new SkippedViolation($event->dependency, $event->dependerLayer, $dependentLayer));
        } else {
            $result->add(new Violation($event->dependency, $event->dependerLayer, $dependentLayer));
        }
    }
}
