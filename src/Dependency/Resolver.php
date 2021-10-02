<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\Configuration\ConfigurationAnalyser;
use Qossmic\Deptrac\Dependency\Event\PostEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PostFlattenEvent;
use Qossmic\Deptrac\Dependency\Event\PreEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PreFlattenEvent;
use Qossmic\Deptrac\DependencyEmitter\ClassDependencyEmitter;
use Qossmic\Deptrac\DependencyEmitter\ClassSuperglobalDependencyEmitter;
use Qossmic\Deptrac\DependencyEmitter\DependencyEmitterInterface;
use Qossmic\Deptrac\DependencyEmitter\FileDependencyEmitter;
use Qossmic\Deptrac\DependencyEmitter\FunctionDependencyEmitter;
use Qossmic\Deptrac\DependencyEmitter\FunctionSuperglobalDependencyEmitter;
use Qossmic\Deptrac\DependencyEmitter\UsesDependencyEmitter;
use Qossmic\Deptrac\Exception\ShouldNotHappenException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Resolver
{
    private EventDispatcherInterface $dispatcher;
    private InheritanceFlatter $inheritanceFlatter;

    /** @var DependencyEmitterInterface[] */
    private array $emitters;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        InheritanceFlatter $inheritanceFlatter,
        ClassDependencyEmitter $classDependencyEmitter,
        ClassSuperglobalDependencyEmitter $classSuperglobalDependencyEmitter,
        FileDependencyEmitter $fileDependencyEmitter,
        FunctionDependencyEmitter $functionDependencyEmitter,
        FunctionSuperglobalDependencyEmitter $functionSuperglobalDependencyEmitter,
        UsesDependencyEmitter $usesDependencyEmitter
    ) {
        $this->dispatcher = $dispatcher;
        $this->inheritanceFlatter = $inheritanceFlatter;
        $this->emitters = [
            ConfigurationAnalyser::CLASS_TOKEN => $classDependencyEmitter,
            ConfigurationAnalyser::CLASS_SUPERGLOBAL_TOKEN => $classSuperglobalDependencyEmitter,
            ConfigurationAnalyser::FILE_TOKEN => $fileDependencyEmitter,
            ConfigurationAnalyser::FUNCTION_TOKEN => $functionDependencyEmitter,
            ConfigurationAnalyser::FUNCTION_SUPERGLOBAL_TOKEN => $functionSuperglobalDependencyEmitter,
            ConfigurationAnalyser::USE_TOKEN => $usesDependencyEmitter,
        ];
    }

    public function resolve(AstMap $astMap, ConfigurationAnalyser $configurationAnalyser): Result
    {
        $result = new Result();

        foreach ($configurationAnalyser->getTypes() as $type) {
            if (!array_key_exists($type, $this->emitters)) {
                throw new ShouldNotHappenException();
            }

            $this->dispatcher->dispatch(new PreEmitEvent($this->emitters[$type]->getName()));
            $this->emitters[$type]->applyDependencies($astMap, $result);
            $this->dispatcher->dispatch(new PostEmitEvent());
        }

        $this->dispatcher->dispatch(new PreFlattenEvent());
        $this->inheritanceFlatter->flattenDependencies($astMap, $result);
        $this->dispatcher->dispatch(new PostFlattenEvent());

        return $result;
    }
}
