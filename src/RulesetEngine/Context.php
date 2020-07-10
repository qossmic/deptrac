<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\RulesetEngine;

final class Context
{
    /**
     * @var Rule[]
     */
    private $rules;

    /**
     * @param Rule[] $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @return Rule[]
     */
    public function all(): array
    {
        return $this->rules;
    }

    /**
     * @return Violation[]
     */
    public function violations(): array
    {
        return array_filter($this->rules, static fn (Rule $rule) => $rule instanceof Violation);
    }

    public function hasViolations(): bool
    {
        return count($this->violations()) > 0;
    }

    /**
     * @return SkippedViolation[]
     */
    public function skippedViolations(): array
    {
        return array_filter($this->rules, static fn (Rule $rule) => $rule instanceof SkippedViolation);
    }

    /**
     * @return Uncovered[]
     */
    public function uncovered(): array
    {
        return array_filter($this->rules, static fn (Rule $rule) => $rule instanceof Uncovered);
    }

    public function hasUncovered(): bool
    {
        return count($this->uncovered()) > 0;
    }

    /**
     * @return Allowed[]
     */
    public function allowed(): array
    {
        return array_filter($this->rules, static fn (Rule $rule) => $rule instanceof Allowed);
    }
}
