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
    private LayerProvider $layerProvider;

    public function __construct(LayerProvider $layerProvider)
    {
        $this->layerProvider = $layerProvider;
    }

    public function __invoke(ProcessEvent $event): void
    {
        $dependency = $event->getDependency();
        $dependerLayer = $event->getDependerLayer();
        $ruleset = $event->getResult();

        foreach ($event->getDependentLayers() as $dependentLayer => $isPublic) {
            try {
                $allowedLayers = $this->layerProvider->getAllowedLayers($dependerLayer);
            } catch (CircularReferenceException $circularReferenceException) {
                $ruleset->addError(new Error($circularReferenceException->getMessage()));
                $event->stopPropagation();

                return;
            }

            if (!$isPublic && $dependerLayer !== $dependentLayer) {
                return;
            }

            if (!in_array($dependentLayer, $allowedLayers, true)) {
                return;
            }

            if ($dependerLayer !== $dependentLayer) {
                $dependentReference = $event->getDependentReference();
                if (($dependentReference instanceof ClassLikeReference)
                    && $dependentReference->isInternal()
                ) {
                    return;
                }
            }

            $ruleset->add(new Allowed($dependency, $dependerLayer, $dependentLayer));

            $event->stopPropagation();
        }
    }
}
