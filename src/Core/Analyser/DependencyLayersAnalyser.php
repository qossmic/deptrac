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
    private AstMapExtractor $astMapExtractor;
    private DependencyResolver $dependencyResolver;
    private TokenResolver $tokenResolver;
    private LayerResolverInterface $dependerLayerResolver;
    private LayerResolverInterface $dependentLayerResolver;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        AstMapExtractor $astMapExtractor,
        DependencyResolver $dependencyResolver,
        TokenResolver $tokenResolver,
        LayerResolverInterface $dependerLayerResolver,
        LayerResolverInterface $dependentLayerResolver,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->astMapExtractor = $astMapExtractor;
        $this->dependencyResolver = $dependencyResolver;
        $this->tokenResolver = $tokenResolver;
        $this->dependerLayerResolver = $dependerLayerResolver;
        $this->dependentLayerResolver = $dependentLayerResolver;
        $this->eventDispatcher = $eventDispatcher;
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
            $dependerLayers = array_keys($this->dependerLayerResolver->getLayersForReference($dependerRef, $astMap));

            if (!isset($warnings[$depender->toString()]) && count($dependerLayers) > 1) {
                $warnings[$depender->toString()] = Warning::tokenIsInMoreThanOneLayer($depender->toString(), $dependerLayers);
            }

            $dependent = $dependency->getDependent();
            $dependentRef = $this->tokenResolver->resolve($dependent, $astMap);
            $dependentLayers = $this->dependentLayerResolver->getLayersForReference($dependentRef, $astMap);

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
