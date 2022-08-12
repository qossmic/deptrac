<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Analyser;

use Qossmic\Deptrac\Contract\Result\Result;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenReferenceInterface;
use Qossmic\Deptrac\Core\Dependency\DependencyInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ProcessEvent extends Event
{
    /**
     * @param array<string, bool> $dependentLayers
     */
    public function __construct(private readonly DependencyInterface $dependency, private readonly TokenReferenceInterface $dependerReference, private readonly string $dependerLayer, private readonly TokenReferenceInterface $dependentReference, private readonly array $dependentLayers, private Result $result = new Result())
    {
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
     * @return array<string, bool>
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
