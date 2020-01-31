<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Configuration;

use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;

/**
 * @author Dmitry Balabka <dmitry.balabka@gmail.com>
 */
final class ConfigurationSkippedViolation
{
    /** @var array<string, string[]> */
    private $classesDeps;

    /**
     * @param array<string, string[]> $arr
     */
    public static function fromArray(array $arr): self
    {
        return new static($arr);
    }

    /**
     * @param array<string, string[]> $classesDeps
     */
    private function __construct(array $classesDeps)
    {
        $this->classesDeps = $classesDeps;
    }

    public function isViolationSkipped(ClassLikeName $classA, ClassLikeName $classB): bool
    {
        $classLikeNameA = (string) $classA;
        $classLikeNameB = (string) $classB;

        return isset($this->classesDeps[$classLikeNameA])
            && \in_array($classLikeNameB, $this->classesDeps[$classLikeNameA], true);
    }
}
