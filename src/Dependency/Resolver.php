<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Dependency;

use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\Dependency\Event\PostEmitEvent;
use SensioLabs\Deptrac\Dependency\Event\PostFlattenEvent;
use SensioLabs\Deptrac\Dependency\Event\PreEmitEvent;
use SensioLabs\Deptrac\Dependency\Event\PreFlattenEvent;
use SensioLabs\Deptrac\DependencyEmitter\DependencyEmitterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Resolver
{
    private $dispatcher;
    private $inheritanceFlatter;

    /** @var DependencyEmitterInterface[] */
    private $emitters;

    /**
     * @param DependencyEmitterInterface[] $emitters
     */
    public function __construct(EventDispatcherInterface $dispatcher, InheritanceFlatter $inheritanceFlatter, $emitters)
    {
        $this->dispatcher = $dispatcher;
        $this->inheritanceFlatter = $inheritanceFlatter;

        foreach ($emitters as $emitter) {
            $this->addEmitter($emitter);
        }
    }

    public function resolve(AstMap $astMap): Result
    {
        $result = new Result();

        foreach ($this->emitters as $emitter) {
            $this->dispatcher->dispatch(new PreEmitEvent($emitter->getName()));
            $emitter->applyDependencies($astMap, $result);
        }
        $this->dispatcher->dispatch(new PostEmitEvent());

        $this->dispatcher->dispatch(new PreFlattenEvent());
        $this->inheritanceFlatter->flattenDependencies($astMap, $result);
        $this->dispatcher->dispatch(new PostFlattenEvent());

        return $result;
    }

    private function addEmitter(DependencyEmitterInterface $emitter): void
    {
        $this->emitters[] = $emitter;
    }
}
