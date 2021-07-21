<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

final class ConfigurationSkippedViolation
{
    /** @var array<string, string[]> */
    private array $classesDeps;

    /**
     * @param array<string, string[]> $arr
     */
    public static function fromArray(array $arr): self
    {
        return new self($arr);
    }

    /**
     * @param array<string, string[]> $classesDeps
     */
    private function __construct(array $classesDeps)
    {
        $this->classesDeps = $classesDeps;
    }

    /**
     * @return array<string, string[]>
     */
    public function all(): array
    {
        return $this->classesDeps;
    }
}
