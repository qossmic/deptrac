<?php

namespace DependencyTracker;

use DependencyTracker\Configuration\ConfigurationRuleset;
use DependencyTracker\RulesetEngine\RulesetViolation;

class RulesetEngine
{
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

                    if (in_array($layerNameOfDependency, $configurationRuleset->getAllowedDependenvies($layerName))) {
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
