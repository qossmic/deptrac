<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Dependency;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Qossmic\Deptrac\Contract\Dependency\PostEmitEvent;
use Qossmic\Deptrac\Contract\Dependency\PostFlattenEvent;
use Qossmic\Deptrac\Contract\Dependency\PreEmitEvent;
use Qossmic\Deptrac\Contract\Dependency\PreFlattenEvent;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Dependency\Emitter\DependencyEmitterInterface;

class DependencyResolver
{
    /**
     * @param array{types: array<string>} $config
     */
    public function __construct(
        private readonly array $config,
        private readonly InheritanceFlattener $inheritanceFlattener,
        private readonly ContainerInterface $emitterLocator,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @throws InvalidEmitterConfigurationException
     */
    public function resolve(AstMap $astMap): DependencyList
    {
        $result = new DependencyList();

        foreach ($this->config['types'] as $type) {
            try {
                $emitter = $this->emitterLocator->get($type);
            } catch (ContainerExceptionInterface) {
                throw InvalidEmitterConfigurationException::couldNotLocate($type);
            }
            if (!$emitter instanceof DependencyEmitterInterface) {
                throw InvalidEmitterConfigurationException::isNotEmitter($type, $emitter);
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
