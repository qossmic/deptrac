<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\Contracts\Dependency\EmitterInterface;
use Qossmic\Deptrac\Dependency\Event\PostEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PostFlattenEvent;
use Qossmic\Deptrac\Dependency\Event\PreEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PreFlattenEvent;
use Qossmic\Deptrac\Exception\Dependency\EmitterResolverException;
use Qossmic\Deptrac\Runtime\Analysis\AnalysisContext;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Resolver
{
    private EventDispatcherInterface $dispatcher;
    private InheritanceFlatter $inheritanceFlatter;
    private AnalysisContext $analysisContext;
    private ContainerInterface $emitterLocator;

    public function __construct(EventDispatcherInterface $dispatcher,
        InheritanceFlatter $inheritanceFlatter,
        AnalysisContext $analysisContext,
        ContainerInterface $emitterLocator)
    {
        $this->dispatcher = $dispatcher;
        $this->inheritanceFlatter = $inheritanceFlatter;
        $this->analysisContext = $analysisContext;
        $this->emitterLocator = $emitterLocator;
    }

    public function resolve(AstMap $astMap): Result
    {
        $result = new Result();

        foreach ($this->analysisContext->getTypes() as $type) {
            try {
                /** @var EmitterInterface $emitter */
                $emitter = $this->emitterLocator->get($type);
            } catch (ContainerExceptionInterface|NotFoundExceptionInterface $containerException) {
                throw EmitterResolverException::missingServiceForType($type, $containerException);
            }

            $this->dispatcher->dispatch(new PreEmitEvent(get_class($emitter)));
            $emitter->applyDependencies($astMap, $result);
            $this->dispatcher->dispatch(new PostEmitEvent());
        }

        $this->dispatcher->dispatch(new PreFlattenEvent());
        $this->inheritanceFlatter->flattenDependencies($astMap, $result);
        $this->dispatcher->dispatch(new PostFlattenEvent());

        return $result;
    }
}
