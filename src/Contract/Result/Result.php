<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Result;

use function get_class;
use function spl_object_id;

class Result
{
    /**
     * @var array<string, array<int, RuleInterface>>
     */
    private array $rules = [];

    /**
     * @var Warning[]
     */
    private array $warnings = [];

    /**
     * @var Error[]
     */
    private array $errors = [];

    public function add(RuleInterface $rule): void
    {
        $this->rules[get_class($rule)][spl_object_id($rule)] = $rule;
    }

    public function remove(RuleInterface $rule): void
    {
        unset($this->rules[get_class($rule)][spl_object_id($rule)]);
    }

    /**
     * @return array<string, array<int, RuleInterface>>
     */
    public function all(): array
    {
        return $this->rules;
    }

    /**
     * @param class-string $type
     *
     * @return array<int, RuleInterface>
     */
    public function allOf(string $type): array
    {
        return $this->rules[$type] ?? [];
    }

    /**
     * @param Warning[] $warnings
     */
    public function addWarnings(array $warnings): void
    {
        foreach ($warnings as $warning) {
            $this->addWarning($warning);
        }
    }

    public function addWarning(Warning $warning): void
    {
        $this->warnings[] = $warning;
    }

    /**
     * @return Warning[]
     */
    public function warnings(): array
    {
        return $this->warnings;
    }

    public function hasWarnings(): bool
    {
        return [] !== $this->warnings;
    }

    /**
     * @param Error[] $errors
     */
    public function addErrors(array $errors): void
    {
        foreach ($errors as $error) {
            $this->addError($error);
        }
    }

    public function addError(Error $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * @return Error[]
     */
    public function errors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return [] !== $this->errors;
    }
}
