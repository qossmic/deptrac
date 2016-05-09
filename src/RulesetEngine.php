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

                    if (in_array(
                        $layerNameOfDependency,
                        $configurationRuleset->getAllowedDependendencies($layerName->getPathname())
                    )) {
                        continue;
                    }

                    $violations[] = new RulesetViolation(
                        $dependency,
                        $layerName->getPathname(),
                        $layerNameOfDependency->getPathname(),
                        ''
                    );
                }
            }
        }

        return $violations;
    }
}
