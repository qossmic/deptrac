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
    public function getViolations(DependencyResult $dependencyResult, ClassNameLayerResolverInterface $classNameLayerResolver, ConfigurationRuleset $configurationRuleset)
    {
        $violations = [];

        foreach ($dependencyResult->getDependenciesAndInheritDependencies() as $dependency) {
            $layerNames = $classNameLayerResolver->getLayersByClassName($dependency->getClassA());

            foreach ($layerNames as $layerName) {
                foreach ($classNameLayerResolver->getLayersByClassName($dependency->getClassB()) as $layerNameOfDependency) {
                    if ($layerName == $layerNameOfDependency) {
                        continue;
                    }

                    $allowedDependencies = $configurationRuleset->getAllowedDependendencies($layerName);

                    if ($this->definesDependencyTypes($allowedDependencies)) {
                        if ($this->isAllowedDependency($allowedDependencies, $layerNameOfDependency, $dependency->getType())) {
                            continue;
                        }
                    } else if (in_array($layerNameOfDependency, $allowedDependencies)) {
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
     * Determines whether the allowed dependencies define include or exclude filters
     * based on dependency types.
     *
     * @param array $allowedDependencies
     * @return bool
     */
    private function definesDependencyTypes(array $allowedDependencies)
    {
        return count($allowedDependencies) > 0
            && is_string(key($allowedDependencies));
    }

    /**
     * Returns true iff the given dependency and its associated layer matches an allowed
     * dependency that uses include or exclude filters based on dependency types.
     *
     * @param array $allowedDependencies
     * @param $layerNameOfDependency
     * @param $typeOfDependency
     * @return bool
     */
    private function isAllowedDependency(array $allowedDependencies, $layerNameOfDependency, $typeOfDependency)
    {
        foreach ($allowedDependencies as $allowedLayerName => $allowedDependencyTypes) {
            if ($layerNameOfDependency != $allowedLayerName) {
                continue;
            }

            if ($allowedDependencyTypes == null) {
                return true; // Convention: "LayerName: ~" whitelists all dependency types
            }

            if (isset($allowedDependencyTypes['include'])) {
                return in_array($typeOfDependency, $allowedDependencyTypes['include']);
            } else if($allowedDependencyTypes['exclude']) {
                return !in_array($typeOfDependency, $allowedDependencyTypes['exclude']);
            }
        }

        return false;
    }
}
