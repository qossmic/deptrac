<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser\EventHandler;

use function in_array;

/**
 * @psalm-immutable
 *
 * @internal
 */
final class SkippedViolationHelper
{
    /**
     * @var array<string, string[]>
     */
    private array $unmatchedSkippedViolation;

    /**
     * @param array<string, string[]> $skippedViolations
     */
    public function __construct(private readonly array $skippedViolations)
    {
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
}
