<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Analyser;

use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Contract\Dependency\DependencyInterface;
use Qossmic\Deptrac\Contract\Result\Result;
use Symfony\Contracts\EventDispatcher\Event;

class ProcessEvent extends Event
{
    /**
     * @param array<string, bool> $dependentLayers
     */
    public function __construct(
        public readonly DependencyInterface $dependency,
        public readonly TokenReferenceInterface $dependerReference,
        public readonly string $dependerLayer,
        public readonly TokenReferenceInterface $dependentReference,
        public readonly array $dependentLayers,
        private Result $result = new Result()
    ) {
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
