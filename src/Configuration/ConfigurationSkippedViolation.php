<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Configuration;

/**
 * @author Dmitry Balabka <dmitry.balabka@gmail.com>
 */
class ConfigurationSkippedViolation
{
    private $classesDeps = [];

    public static function fromArray(array $arr): self
    {
        return new static($arr);
    }

    /**
     * @param string[] $classesDeps
     */
    private function __construct(array $classesDeps)
    {
        $this->classesDeps = $classesDeps;
    }

    public function isViolationSkipped(string $classA, string $classB): bool
    {
        return isset($this->classesDeps[$classA]) && \in_array($classB, $this->classesDeps[$classA], true);
    }
}
