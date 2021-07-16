<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\Configuration\ConfigurationAnalyzer;
use Qossmic\Deptrac\Dependency\Event\PostEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PostFlattenEvent;
use Qossmic\Deptrac\Dependency\Event\PreEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PreFlattenEvent;
use Qossmic\Deptrac\DependencyEmitter\ClassDependencyEmitter;
use Qossmic\Deptrac\DependencyEmitter\UsesDependencyEmitter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Resolver
{
    private EventDispatcherInterface $dispatcher;
    private InheritanceFlatter $inheritanceFlatter;
    private ClassDependencyEmitter $classDependencyEmitter;
    private UsesDependencyEmitter $usesDependencyEmitter;

    public function __construct(EventDispatcherInterface $dispatcher, InheritanceFlatter $inheritanceFlatter, ClassDependencyEmitter $classDependencyEmitter, UsesDependencyEmitter $usesDependencyEmitter)
    {
        $this->dispatcher = $dispatcher;
        $this->inheritanceFlatter = $inheritanceFlatter;
        $this->classDependencyEmitter = $classDependencyEmitter;
        $this->usesDependencyEmitter = $usesDependencyEmitter;
    }

    public function resolve(AstMap $astMap, ConfigurationAnalyzer $configurationAnalyzer): Result
    {
        $result = new Result();

        $types = $configurationAnalyzer->getTypes();

        if(in_array('class', $types, true)) {
            $this->dispatcher->dispatch(new PreEmitEvent($this->classDependencyEmitter->getName()));
            $this->classDependencyEmitter->applyDependencies($astMap, $result);
            $this->dispatcher->dispatch(new PostEmitEvent());
        }
        if(in_array('uses', $types, true)) {
            $this->dispatcher->dispatch(new PreEmitEvent($this->usesDependencyEmitter->getName()));
            $this->usesDependencyEmitter->applyDependencies($astMap, $result);
            $this->dispatcher->dispatch(new PostEmitEvent());
        }
        //TODO: FunctionEmitter + FileEmitter (Patrick Kusebauch @ 16.07.21)

        $this->dispatcher->dispatch(new PreFlattenEvent());
        $this->inheritanceFlatter->flattenDependencies($astMap, $result);
        $this->dispatcher->dispatch(new PostFlattenEvent());

        return $result;
    }
}
