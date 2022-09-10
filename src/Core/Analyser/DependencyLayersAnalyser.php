<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser;

use Psr\EventDispatcher\EventDispatcherInterface;
use Qossmic\Deptrac\Contract\Analyser\PostProcessEvent;
use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use Qossmic\Deptrac\Contract\Result\Result;
use Qossmic\Deptrac\Contract\Result\Warning;
use Qossmic\Deptrac\Core\Dependency\DependencyResolver;
use Qossmic\Deptrac\Core\Dependency\TokenResolver;
use Qossmic\Deptrac\Core\Layer\LayerResolverInterface;
use function count;

class DependencyLayersAnalyser
{
    public function __construct(
        private readonly AstMapExtractor $astMapExtractor,
        private readonly DependencyResolver $dependencyResolver,
        private readonly TokenResolver $tokenResolver,
        private readonly LayerResolverInterface $dependerLayerResolver,
        private readonly LayerResolverInterface $dependentLayerResolver,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function process(): Result
    {
        $astMap = $this->astMapExtractor->extract();

        $dependencies = $this->dependencyResolver->resolve($astMap);

        $result = new Result();
        $warnings = [];

        foreach ($dependencies->getDependenciesAndInheritDependencies() as $dependency) {
            $depender = $dependency->getDepender();
            $dependerRef = $this->tokenResolver->resolve($depender, $astMap);
            $dependerLayers = array_keys($this->dependerLayerResolver->getLayersForReference($dependerRef));

            if (!isset($warnings[$depender->toString()]) && count($dependerLayers) > 1) {
                $warnings[$depender->toString()] = Warning::tokenIsInMoreThanOneLayer($depender->toString(), $dependerLayers);
            }

            $dependent = $dependency->getDependent();
            $dependentRef = $this->tokenResolver->resolve($dependent, $astMap);
            $dependentLayers = $this->dependentLayerResolver->getLayersForReference($dependentRef);

            foreach ($dependerLayers as $dependentLayer) {
                $event = new ProcessEvent($dependency, $dependerRef, $dependentLayer, $dependentRef, $dependentLayers, $result);
                $this->eventDispatcher->dispatch($event);

                $result = $event->getResult();
            }
        }

        $result->addWarnings($warnings);

        $event = new PostProcessEvent($result);
        $this->eventDispatcher->dispatch($event);

        return $event->getResult();
    }
}
