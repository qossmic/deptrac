<?php

namespace SensioLabs\Deptrac;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;

class DependencyContext
{
    /** @var AstMap */
    private $astMap;

    /** @var RulesetViolation[] */
    private $violations;

    /** @var DependencyResult */
    private $dependencyResult;

    /** @var ClassNameLayerResolverInterface */
    private $classNameLayerResolver;

    /**
     * DependencyContext constructor.
     *
     * @param AstMap                           $astMap
     * @param RulesetEngine\RulesetViolation[] $violations
     * @param DependencyResult                 $dependencyResult
     * @param ClassNameLayerResolverInterface  $classNameLayerResolver
     */
    public function __construct(
        AstMap $astMap,
        array $violations,
        DependencyResult $dependencyResult,
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

    public function getDependencyResult(): DependencyResult
    {
        return $this->dependencyResult;
    }

    public function getClassNameLayerResolver(): ClassNameLayerResolverInterface
    {
        return $this->classNameLayerResolver;
    }
}
