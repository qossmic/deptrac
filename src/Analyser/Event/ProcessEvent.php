<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Analyser\Event;

use Qossmic\Deptrac\Ast\AstMap\TokenReferenceInterface;
use Qossmic\Deptrac\Dependency\DependencyInterface;
use Qossmic\Deptrac\Result\Result;
use Symfony\Contracts\EventDispatcher\Event;

class ProcessEvent extends Event
{
    private DependencyInterface $dependency;

    private TokenReferenceInterface $dependerReference;

    private string $dependerLayer;

    private TokenReferenceInterface $dependentReference;

    /**
     * @var string[]
     */
    private array $dependentLayers;

    private Result $result;

    /**
     * @param string[] $dependentLayers
     */
    public function __construct(
        DependencyInterface $dependency,
        TokenReferenceInterface $dependerReference,
        string $dependerLayer,
        TokenReferenceInterface $dependentReference,
        array $dependentLayers,
        ?Result $result = null
    ) {
        $this->dependency = $dependency;
        $this->dependerReference = $dependerReference;
        $this->dependerLayer = $dependerLayer;
        $this->dependentReference = $dependentReference;
        $this->dependentLayers = $dependentLayers;
        $this->result = $result ?? new Result();
    }

    public function getDependency(): DependencyInterface
    {
        return $this->dependency;
    }

    public function getDependerReference(): TokenReferenceInterface
    {
        return $this->dependerReference;
    }

    public function getDependerLayer(): string
    {
        return $this->dependerLayer;
    }

    public function getDependentReference(): TokenReferenceInterface
    {
        return $this->dependentReference;
    }

    /**
     * @return string[]
     */
    public function getDependentLayers(): array
    {
        return $this->dependentLayers;
    }

    public function getResult(): Result
    {
        return $this->result;
    }

    public function replaceResult(Result $ruleset): void
    {
        $this->result = $ruleset;
    }
}
