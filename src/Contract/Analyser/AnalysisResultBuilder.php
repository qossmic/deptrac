<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Analyser;

use Qossmic\Deptrac\Contract\Result\Error;
use Qossmic\Deptrac\Contract\Result\OutputResult;
use Qossmic\Deptrac\Contract\Result\RuleInterface;
use Qossmic\Deptrac\Contract\Result\Warning;

use function spl_object_id;

class AnalysisResultBuilder
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
        $this->rules[$rule::class][spl_object_id($rule)] = $rule;
    }

    public function remove(RuleInterface $rule): void
    {
        unset($this->rules[$rule::class][spl_object_id($rule)]);
    }

    /**
     * @return array<string, array<int, RuleInterface>>
     */
    public function all(): array
    {
        return $this->rules;
    }

    public function build(): OutputResult
    {
        return new OutputResult($this->rules, $this->errors, $this->warnings);
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
