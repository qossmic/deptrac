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
     * @var Error[]
     */
    private $errors;

    /**
     * @param Rule[]  $rules
     * @param Error[] $errors
     */
    public function __construct(array $rules, array $errors)
    {
        $this->rules = $rules;
        $this->errors = $errors;
    }

    /**
     * @return Rule[]
     */
    public function rules(): array
    {
        return $this->rules;
    }

    /**
     * @return Violation[]
     */
    public function violations(): array
    {
        return array_filter($this->rules, static function (Rule $rule) {
            return $rule instanceof Violation;
        });
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
        return array_filter($this->rules, static function (Rule $rule) {
            return $rule instanceof SkippedViolation;
        });
    }

    /**
     * @return Uncovered[]
     */
    public function uncovered(): array
    {
        return array_filter($this->rules, static function (Rule $rule) {
            return $rule instanceof Uncovered;
        });
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
        return array_filter($this->rules, static function (Rule $rule) {
            return $rule instanceof Allowed;
        });
    }

    public function hasErrors(): bool
    {
        return \count($this->errors) > 0;
    }

    /**
     * @return Error[]
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
