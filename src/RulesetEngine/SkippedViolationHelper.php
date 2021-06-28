<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\RulesetEngine;

use Qossmic\Deptrac\AstRunner\AstMap\TokenName;
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

    public function isViolationSkipped(TokenName $tokenNameA, TokenName $tokenNameB): bool
    {
        $a = $tokenNameA->toString();
        $b = $tokenNameB->toString();

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
