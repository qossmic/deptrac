<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\RulesetEngine;

use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\Configuration\ConfigurationSkippedViolation;

final class SkippedViolationHelper
{
    /**
     * @var array<string, string[]>
     */
    private $skippedViolation;

    /**
     * @var array<string, string[]>
     */
    private $unmatchedSkippedViolation;

    public function __construct(ConfigurationSkippedViolation $configuration)
    {
        $this->skippedViolation = $configuration->all();
        $this->unmatchedSkippedViolation = $configuration->all();
    }

    public function isViolationSkipped(ClassLikeName $classLikeNameA, ClassLikeName $classLikeNameB): bool
    {
        $a = $classLikeNameA->toString();
        $b = $classLikeNameB->toString();

        $matched = isset($this->skippedViolation[$a]) && \in_array($b, $this->skippedViolation[$a], true);

        if ($matched && false !== ($key = array_search($b, $this->unmatchedSkippedViolation[$a], true))) {
            unset($this->unmatchedSkippedViolation[$a][$key]);
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
}
