<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser\EventHandler;

use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use Qossmic\Deptrac\Contract\Result\Allowed;
use Qossmic\Deptrac\Contract\Result\Error;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Layer\Exception\CircularReferenceException;
use Qossmic\Deptrac\Core\Layer\LayerProvider;
use function in_array;

/**
 * @internal
 */
class AllowDependencyHandler
{
    public function __construct(private readonly LayerProvider $layerProvider)
    {
    }

    public function __invoke(ProcessEvent $event): void
    {
        $ruleset = $event->getResult();

        foreach ($event->dependentLayers as $dependentLayer => $isPublic) {
            try {
                $allowedLayers = $this->layerProvider->getAllowedLayers($event->dependerLayer);
            } catch (CircularReferenceException $circularReferenceException) {
                $ruleset->addError(new Error($circularReferenceException->getMessage()));
                $event->stopPropagation();

                return;
            }

            if (!$isPublic && $event->dependerLayer !== $dependentLayer) {
                return;
            }

            if (!in_array($dependentLayer, $allowedLayers, true)) {
                return;
            }

            if ($event->dependerLayer !== $dependentLayer
                && $event->dependentReference instanceof ClassLikeReference
                && $event->dependentReference->isInternal
            ) {
                return;
            }

            $ruleset->add(new Allowed($event->dependency, $event->dependerLayer, $dependentLayer));

            $event->stopPropagation();
        }
    }
}
