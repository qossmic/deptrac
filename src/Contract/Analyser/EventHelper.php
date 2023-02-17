<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Analyser;

use Qossmic\Deptrac\Contract\Layer\LayerProvider;
use Qossmic\Deptrac\Contract\Result\SkippedViolation;
use Qossmic\Deptrac\Contract\Result\Violation;

class EventHelper
{
    /**
     * @var array<string, string[]>
     */
    private array $unmatchedSkippedViolation;

    /**
     * @param array<string, string[]> $skippedViolations
     */
    public function __construct(
        private readonly array $skippedViolations,
        public readonly LayerProvider $layerProvider,
    ) {
        $this->unmatchedSkippedViolation = $skippedViolations;
    }

    public function isViolationSkipped(string $depender, string $dependent): bool
    {
        $matched = isset($this->skippedViolations[$depender]) && in_array($dependent, $this->skippedViolations[$depender], true);

        if ($matched && false !== ($key = array_search($dependent, $this->unmatchedSkippedViolation[$depender], true))) {
            unset($this->unmatchedSkippedViolation[$depender][$key]);
        }

        return $matched;
    }

    /**
     * @return array<string, string[]>
     */
    public function unmatchedSkippedViolations(): array
    {
        return array_filter($this->unmatchedSkippedViolation);
    }

    public function addSkippableViolation(ProcessEvent $event, AnalysisResult $result, string $dependentLayer, ViolationCreatingInterface $violationCreatingRule): void
    {
        if ($this->isViolationSkipped(
            $event->dependency->getDepender()
                ->toString(),
            $event->dependency->getDependent()
                ->toString()
        )
        ) {
            $result->addRule(new SkippedViolation($event->dependency, $event->dependerLayer, $dependentLayer));
        } else {
            $result->addRule(new Violation($event->dependency, $event->dependerLayer, $dependentLayer, $violationCreatingRule));
        }
    }
}
