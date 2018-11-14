<?php

namespace SensioLabs\Deptrac;

use SensioLabs\AstRunner\AstMap;
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

    /**
     * DependencyContext constructor.
     *
     * @param RulesetEngine\RulesetViolation[] $violations
     */
    public function __construct(
        AstMap $astMap,
        array $violations,
        Result $dependencyResult,
        ClassNameLayerResolverInterface $classNameLayerResolver
    ) {
        $this->astMap = $astMap;
        $this->violations = $violations;
        $this->dependencyResult = $dependencyResult;
        $this->classNameLayerResolver = $classNameLayerResolver;
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
}
