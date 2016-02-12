<?php

namespace DependencyTracker;

use DependencyTracker\Configuration\ConfigurationRuleset;
use DependencyTracker\RulesetEngine\RulesetViolation;

class RulesetEngine
{
    /**
     * @param DependencyResult $dependencyResult
     * @param ClassNameLayerResolver $classNameLayerResolver
     * @param ConfigurationRuleset $configurationRuleset
     * @return RulesetViolation[]
     */
    public function getViolations(DependencyResult $dependencyResult, ClassNameLayerResolver $classNameLayerResolver, ConfigurationRuleset $configurationRuleset)
    {
        $violations = [];

        foreach ($dependencyResult->getDependenciesAndInheritDependencies() as $dependency) {

            $layerNames = $classNameLayerResolver->getLayersByClassName($dependency->getClassA());

            foreach ($layerNames as $layerName) {
                foreach ($classNameLayerResolver->getLayersByClassName($dependency->getClassB()) as $layerNameOfDependency) {

                    if ($layerName == $layerNameOfDependency) {
                        continue;
                    }

                    if (in_array(
                        $layerNameOfDependency,
                        $configurationRuleset->getAllowedDependendencies($layerName)
                    )) {
                        continue;
                    }

                    $violations[] = new RulesetViolation(
                        $dependency,
                        $layerName,
                        $layerNameOfDependency,
                        ''
                    );
                }
            }

        }

        return $violations;
    }

}
