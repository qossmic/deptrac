<?php

namespace DependencyTracker;

use DependencyTracker\Configuration\ConfigurationRuleset;
use DependencyTracker\RulesetEngine\RulesetViolation;

class RulesetEngine
{
    /**
     * @param DependencyResult $dependencyResult
     * @param ConfigurationRuleset $configurationRuleset
     * @return RulesetViolation[]
     */
    public function getViolations(DependencyResult $dependencyResult, ConfigurationRuleset $configurationRuleset)
    {
        $violations = [];

        foreach ($dependencyResult->getDependencies() as $dependency) {

            $layerNames = $dependencyResult->getLayersByClassName($dependency->getClassA());

            foreach ($layerNames as $layerName) {
                foreach ($dependencyResult->getLayersByClassName($dependency->getClassB()) as $layerNameOfDependency) {

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
