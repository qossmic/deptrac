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
    )
    {
        $violations = [];

        foreach ($dependencyResult->getDependenciesAndInheritDependencies() as $dependency) {
            $layers = $classNameLayerResolver->getLayersByClassName($dependency->getClassA());

            foreach ($layers as $layer) {
                foreach ($classNameLayerResolver->getLayersByClassName($dependency->getClassB()) as $layerOfDependency) {
                    if ($layer == $layerOfDependency) {
                        continue;
                    }

                    if (in_array(
                        $layerOfDependency->getPathname(),
                        $configurationRuleset->getAllowedDependendencies($layer->getPathname())
                    )) {
                        continue;
                    }

                    $violations[] = new RulesetViolation(
                        $dependency,
                        $layer,
                        $layerOfDependency,
                        ''
                    );
                }
            }
        }

        return $violations;
    }
}
