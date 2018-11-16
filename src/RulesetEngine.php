<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\Configuration\ConfigurationRuleset;
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
                foreach ($classNameLayerResolver->getLayersByClassName($dependency->getClassB()) as $layerNameOfDependency) {
                    if ($layerName === $layerNameOfDependency) {
                        continue;
                    }

                    if (in_array(
                        $layerNameOfDependency,
                        $configurationRuleset->getAllowedDependencies($layerName),
                        true
                    )) {
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
}
