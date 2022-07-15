<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Dependency;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Qossmic\Deptrac\Contract\Dependency\PostEmitEvent;
use Qossmic\Deptrac\Contract\Dependency\PostFlattenEvent;
use Qossmic\Deptrac\Contract\Dependency\PreEmitEvent;
use Qossmic\Deptrac\Contract\Dependency\PreFlattenEvent;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Dependency\Emitter\DependencyEmitterInterface;
use Qossmic\Deptrac\Supportive\ShouldNotHappenException;
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
