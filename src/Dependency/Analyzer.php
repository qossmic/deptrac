<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Dependency;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\DependencyEmitter\DependencyEmitterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Analyzer
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

    public function analyze(AstParserInterface $parser, AstMap $astMap): Result
    {
        $result = new Result();

        foreach ($this->emitters as $emitter) {
            $this->dispatcher->dispatch(Events::PRE_EMIT, new PreEmitEvent($emitter->getName()));
            $emitter->applyDependencies($parser, $astMap, $result);
        }
        $this->dispatcher->dispatch(Events::POST_EMIT);

        $this->dispatcher->dispatch(Events::PRE_FLATTEN);
        $this->inheritanceFlatter->flattenDependencies($astMap, $result);
        $this->dispatcher->dispatch(Events::POST_FLATTEN);

        return $result;
    }

    private function addEmitter(DependencyEmitterInterface $emitter): void
    {
        $this->emitters[] = $emitter;
    }
}
