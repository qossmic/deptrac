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

    /** @var Configuration */
    private $configuration;

    /**
     * DependencyContext constructor.
     *
     * @param AstMap                           $astMap
     * @param RulesetEngine\RulesetViolation[] $violations
     * @param DependencyResult                 $dependencyResult
     * @param ClassNameLayerResolverInterface  $classNameLayerResolver
     */
    public function __construct(
        Configuration $configuration,
        AstMap $astMap,
        array $violations,
        DependencyResult $dependencyResult,
        ClassNameLayerResolverInterface $classNameLayerResolver
    ) {
        $this->astMap = $astMap;
        $this->violations = $violations;
        $this->dependencyResult = $dependencyResult;
        $this->classNameLayerResolver = $classNameLayerResolver;
        $this->configuration = $configuration;
    }

    /** @return AstMap */
    public function getAstMap()
    {
        return $this->astMap;
    }

    /** @return RulesetEngine\RulesetViolation[] */
    public function getViolations()
    {
        return $this->violations;
    }

    /** @return DependencyResult */
    public function getDependencyResult()
    {
        return $this->dependencyResult;
    }

    /** @return ClassNameLayerResolverInterface */
    public function getClassNameLayerResolver()
    {
        return $this->classNameLayerResolver;
    }

    /** @return Configuration */
    public function getConfiguration()
    {
        return $this->configuration;
    }

}
