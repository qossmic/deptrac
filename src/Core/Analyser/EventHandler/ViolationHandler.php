<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser\EventHandler;

use Qossmic\Deptrac\Contract\Analyser\PostProcessEvent;
use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use Qossmic\Deptrac\Contract\Result\Error;
use Qossmic\Deptrac\Contract\Result\SkippedViolation;
use Qossmic\Deptrac\Contract\Result\Violation;
use function sprintf;

/**
 * @internal
 */
class ViolationHandler
{
    private readonly SkippedViolationHelper $skippedViolationHelper;

    /**
     * @param array<string, string[]>|null $skippedViolations
     */
    public function __construct(?array $skippedViolations)
    {
        $this->skippedViolationHelper = new SkippedViolationHelper($skippedViolations ?? []);
    }

    public function handleViolation(ProcessEvent $event): void
    {
        $dependency = $event->getDependency();
        $depender = $dependency->getDepender();
        $dependent = $dependency->getDependent();
        $dependerLayer = $event->getDependerLayer();
        $dependentLayers = $event->getDependentLayers();
        $ruleset = $event->getResult();

        foreach ($dependentLayers as $dependentLayer => $_) {
            if ($this->skippedViolationHelper->isViolationSkipped($depender->toString(), $dependent->toString())) {
                $ruleset->add(new SkippedViolation($dependency, $dependerLayer, $dependentLayer));

                continue;
            }

            $ruleset->add(new Violation($dependency, $dependerLayer, $dependentLayer));
        }
    }

    public function handleUnmatchedSkipped(PostProcessEvent $event): void
    {
        $ruleset = $event->getResult();

        foreach ($this->skippedViolationHelper->unmatchedSkippedViolations() as $classLikeNameA => $classLikes) {
            foreach ($classLikes as $classLikeNameB) {
                $ruleset->addError(new Error(sprintf('Skipped violation "%s" for "%s" was not matched.', $classLikeNameB, $classLikeNameA)));
            }
        }
    }
}
