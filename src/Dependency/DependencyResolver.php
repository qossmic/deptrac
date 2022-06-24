<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Dependency\Emitter\DependencyEmitterInterface;
use Qossmic\Deptrac\Events\Dependency\PostEmitEvent;
use Qossmic\Deptrac\Events\Dependency\PostFlattenEvent;
use Qossmic\Deptrac\Events\Dependency\PreEmitEvent;
use Qossmic\Deptrac\Events\Dependency\PreFlattenEvent;
use Qossmic\Deptrac\Utils\ShouldNotHappenException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DependencyResolver
{
    private ContainerInterface $emitterLocator;
    private InheritanceFlattener $inheritanceFlattener;
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @var array{types: array<string>}
     */
    private array $config;

    /**
     * @param array{types: array<string>} $config
     */
    public function __construct(
        array $config,
        InheritanceFlattener $inheritanceFlattener,
        ContainerInterface $emitterLocator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->config = $config;
        $this->inheritanceFlattener = $inheritanceFlattener;
        $this->emitterLocator = $emitterLocator;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function resolve(AstMap $astMap): DependencyList
    {
        $result = new DependencyList();

        foreach ($this->config['types'] as $type) {
            try {
                $emitter = $this->emitterLocator->get($type);
            } catch (ContainerExceptionInterface $containerException) {
                throw new ShouldNotHappenException();
            }
            if (!$emitter instanceof DependencyEmitterInterface) {
                throw new ShouldNotHappenException();
            }

            $this->eventDispatcher->dispatch(new PreEmitEvent($emitter->getName()));
            $emitter->applyDependencies($astMap, $result);
            $this->eventDispatcher->dispatch(new PostEmitEvent());
        }

        $this->eventDispatcher->dispatch(new PreFlattenEvent());
        $this->inheritanceFlattener->flattenDependencies($astMap, $result);
        $this->eventDispatcher->dispatch(new PostFlattenEvent());

        return $result;
    }
}
