<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;

class DependencyContext
{
    /** @var AstMap */
    private $astMap;
    /** @var RulesetViolation[] */
    private $violations;

    /** @var Result */
    private $dependencyResult;

    /** @var ClassNameLayerResolverInterface */
    private $classNameLayerResolver;

    /** @var RulesetViolation[] */
    private $skippedViolations;

    /**
     * DependencyContext constructor.
     *
     * @param RulesetEngine\RulesetViolation[] $violations
     * @param RulesetEngine\RulesetViolation[] $skippedViolations
     */
    public function __construct(
        AstMap $astMap,
        array $violations,
        Result $dependencyResult,
        ClassNameLayerResolverInterface $classNameLayerResolver,
        array $skippedViolations = []
    ) {
        $this->astMap = $astMap;
        $this->violations = $violations;
        $this->dependencyResult = $dependencyResult;
        $this->classNameLayerResolver = $classNameLayerResolver;
        $this->skippedViolations = $skippedViolations;
    }

    public function getAstMap(): AstMap
    {
        return $this->astMap;
    }

    /**
     * @return RulesetEngine\RulesetViolation[]
     */
    public function getViolations(): array
    {
        return $this->violations;
    }

    /**
     * @return RulesetViolation[]
     */
    public function getViolationsByLayerName(string $layerName): array
    {
        return array_filter($this->violations, function (RulesetViolation $violation) use ($layerName) {
            return $violation->getLayerA() === $layerName;
        });
    }

    public function getDependencyResult(): Result
    {
        return $this->dependencyResult;
    }

    public function getClassNameLayerResolver(): ClassNameLayerResolverInterface
    {
        return $this->classNameLayerResolver;
    }

    /**
     * @return RulesetViolation[]
     */
    public function getSkippedViolationsByLayerName(string $layerName): array
    {
        return array_values(
            array_filter(
                $this->skippedViolations,
                function (RulesetViolation $violation) use ($layerName) {
                    return $violation->getLayerA() === $layerName;
                }
            )
        );
    }

    /**
     * @return RulesetViolation[]
     */
    public function getSkippedViolations(): array
    {
        return $this->skippedViolations;
    }

    public function isViolationSkipped(RulesetViolation $violation): bool
    {
        return \in_array($violation, $this->skippedViolations, true);
    }
}
