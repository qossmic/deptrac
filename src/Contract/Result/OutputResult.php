<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Result;

use Qossmic\Deptrac\Contract\Analyser\AnalysisResult;

use function count;

/**
 * @psalm-immutable
 *
 * Represents a result ready for output formatting
 */
final class OutputResult
{
    /**
     * @param array<string, array<int, RuleInterface>> $rules
     * @param Error[] $errors
     * @param Warning[] $warnings
     */
    private function __construct(
        public readonly array $rules,
        public readonly array $errors,
        public readonly array $warnings
    ) {
    }

    public static function fromAnalysisResult(AnalysisResult $analysisResult): self
    {
        return new self($analysisResult->rules(), $analysisResult->errors(), $analysisResult->warnings());
    }

    /**
     * @template T of RuleInterface
     *
     * @param class-string<T> $type
     *
     * @return array<int, T>
     */
    public function allOf(string $type): array
    {
        return $this->rules[$type] ?? [];
    }

    /**
     * @return list<RuleInterface>
     */
    public function allRules(): array
    {
        return array_reduce(
            $this->rules,
            static fn (array $carry, array $rules): array => array_merge($carry, array_values($rules)),
            []
        );
    }

    /**
     * @return array<int, Violation>
     */
    public function violations(): array
    {
        return $this->allOf(Violation::class);
    }

    public function hasViolations(): bool
    {
        return count($this->violations()) > 0;
    }

    /**
     * @return array<int, SkippedViolation>
     */
    public function skippedViolations(): array
    {
        return $this->allOf(SkippedViolation::class);
    }

    /**
     * @return array<int, Uncovered>
     */
    public function uncovered(): array
    {
        return $this->allOf(Uncovered::class);
    }

    public function hasUncovered(): bool
    {
        return count($this->uncovered()) > 0;
    }

    /**
     * @return array<int, Allowed>
     */
    public function allowed(): array
    {
        return $this->allOf(Allowed::class);
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    public function hasWarnings(): bool
    {
        return count($this->warnings) > 0;
    }
}
