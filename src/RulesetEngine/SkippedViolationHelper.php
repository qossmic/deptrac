<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\RulesetEngine;

use Qossmic\Deptrac\Configuration\ConfigurationSkippedViolation;

final class SkippedViolationHelper
{
    /**
     * @var array<string, string[]>
     */
    private array $skippedViolation;

    /**
     * @var array<string, string[]>
     */
    private array $unmatchedSkippedViolation;

    public function __construct(ConfigurationSkippedViolation $configuration)
    {
        $this->skippedViolation = $configuration->all();
        $this->unmatchedSkippedViolation = $configuration->all();
    }

    public function isViolationSkipped(string $dependant, string $dependee): bool
    {
        $matched = isset($this->skippedViolation[$dependant]) && \in_array($dependee, $this->skippedViolation[$dependant], true);

        if ($matched && false !== ($key = array_search($dependee, $this->unmatchedSkippedViolation[$dependant], true))) {
            unset($this->unmatchedSkippedViolation[$dependant][$key]);
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
