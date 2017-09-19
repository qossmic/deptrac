<?php

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\Configuration\ConfigurationRuleset;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;

class RulesetEngine
{
    /**
     * @param DependencyResult                $dependencyResult
     * @param ClassNameLayerResolverInterface $classNameLayerResolver
     * @param ConfigurationRuleset            $configurationRuleset
     *
     * @return RulesetViolation[]
     */
    public function getViolations(
        DependencyResult $dependencyResult,
        ClassNameLayerResolverInterface $classNameLayerResolver,
        ConfigurationRuleset $configurationRuleset
    ) {
        $violations = [];

        foreach ($dependencyResult->getDependenciesAndInheritDependencies() as $dependency) {
            $layerNamesClassA = $classNameLayerResolver->getLayersByClassName($dependency->getClassA());

            foreach ($layerNamesClassA as $layerName) {
                $layerNamesClassB = $classNameLayerResolver->getLayersByClassName($dependency->getClassB());

                foreach ($layerNamesClassB as $layerNameOfDependency) {
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
