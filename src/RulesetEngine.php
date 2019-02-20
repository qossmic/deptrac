<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\Configuration\ConfigurationRuleset;
use SensioLabs\Deptrac\Configuration\ConfigurationSkippedViolation;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;

class RulesetEngine
{
    /**
     * @return RulesetViolation[]
     */
    public function getViolations(Result $dependencyResult, ClassNameLayerResolverInterface $classNameLayerResolver, ConfigurationRuleset $configurationRuleset): array
    {
        $violations = [];

        foreach ($dependencyResult->getDependenciesAndInheritDependencies() as $dependency) {
            $layerNames = $classNameLayerResolver->getLayersByClassName($dependency->getClassA());

            foreach ($layerNames as $layerName) {
                $allowedDependencies = $configurationRuleset->getAllowedDependencies($layerName);

                foreach ($classNameLayerResolver->getLayersByClassName($dependency->getClassB()) as $layerNameOfDependency) {
                    if ($layerName === $layerNameOfDependency) {
                        continue;
                    }

                    if (in_array($layerNameOfDependency, $allowedDependencies, true)) {
                        continue;
                    }

                    $violations[] = new RulesetViolation(
                        $dependency,
                        $layerName,
                        $layerNameOfDependency
                    );
                }
            }
        }

        return $violations;
    }

    /**
     * @param RulesetViolation[] $violations
     *
     * @return RulesetViolation[]
     */
    public function getSkippedViolations(array $violations, ConfigurationSkippedViolation $configurationSkipViolation): array
    {
        return \array_values(
            \array_filter($violations, function ($violation) use ($configurationSkipViolation) {
                /** @var RulesetViolation $violation */
                $dep = $violation->getDependency();

                return $configurationSkipViolation->isViolationSkipped($dep->getClassA(), $dep->getClassB());
            })
        );
    }
}
