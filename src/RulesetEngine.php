<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use InvalidArgumentException;
use JetBrains\PHPStormStub\PhpStormStubsMap;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\TokenLikeName;
use Qossmic\Deptrac\Configuration\Configuration;
use Qossmic\Deptrac\Dependency\Result;
use Qossmic\Deptrac\RulesetEngine\Allowed;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\Error;
use Qossmic\Deptrac\RulesetEngine\SkippedViolation;
use Qossmic\Deptrac\RulesetEngine\SkippedViolationHelper;
use Qossmic\Deptrac\RulesetEngine\Uncovered;
use Qossmic\Deptrac\RulesetEngine\Violation;
use Qossmic\Deptrac\RulesetEngine\Warning;

class RulesetEngine
{
    public function process(
        Result $dependencyResult,
        ClassLikeLayerResolverInterface $classLikeLayerResolver,
        Configuration $configuration
    ): Context {
        $rules = [];
        $warnings = [];
        $errors = [];

        $configurationRuleset = $configuration->getRuleset();
        $skippedViolationHelper = new SkippedViolationHelper($configuration->getSkipViolations());

        foreach ($dependencyResult->getDependenciesAndInheritDependencies() as $dependency) {
            $tokenLikeNameA = $dependency->getTokenLikeNameA();
            assert($tokenLikeNameA instanceof ClassLikeName);
            $layerNames = $classLikeLayerResolver->getLayersByClassLikeName($tokenLikeNameA);

            $classLikeANameString = $tokenLikeNameA->toString();
            if (!isset($warnings[$classLikeANameString]) && count($layerNames) > 1) {
                $warnings[$classLikeANameString] = Warning::tokenLikeIsInMoreThanOneLayer($tokenLikeNameA, $layerNames);
            }

            foreach ($layerNames as $layerName) {
                try {
                    $allowedDependencies = $configurationRuleset->getAllowedDependencies($layerName);
                } catch (InvalidArgumentException $exception) {
                    $errors[] = new Error($exception->getMessage());
                    continue;
                }

                $tokenLikeNameB = $dependency->getTokenLikeNameB();
                assert($tokenLikeNameB instanceof ClassLikeName);
                $layersNamesClassB = $classLikeLayerResolver->getLayersByClassLikeName($tokenLikeNameB);

                if (0 === count($layersNamesClassB)) {
                    if (!$this->ignoreUncoveredInternalClass($configuration, $tokenLikeNameB)) {
                        $rules[] = new Uncovered($dependency, $layerName);
                    }
                    continue;
                }

                foreach ($layersNamesClassB as $layerNameOfDependency) {
                    if ($layerName === $layerNameOfDependency) {
                        continue;
                    }

                    if (in_array($layerNameOfDependency, $allowedDependencies, true)) {
                        $rules[] = new Allowed($dependency, $layerName, $layerNameOfDependency);
                        continue;
                    }

                    if ($skippedViolationHelper->isViolationSkipped($tokenLikeNameA, $tokenLikeNameB)) {
                        $rules[] = new SkippedViolation($dependency, $layerName, $layerNameOfDependency);
                        continue;
                    }

                    $rules[] = new Violation($dependency, $layerName, $layerNameOfDependency);
                }
            }
        }

        foreach ($skippedViolationHelper->unmatchedSkippedViolations() as $classLikeNameA => $classLikes) {
            foreach ($classLikes as $classLikeNameB) {
                $errors[] = new Error(sprintf('Skipped violation "%s" for "%s" was not matched.', $classLikeNameB, $classLikeNameA));
            }
        }

        return new Context($rules, $errors, $warnings);
    }

    private function ignoreUncoveredInternalClass(Configuration $configuration, TokenLikeName $tokenLikeName): bool
    {
        return !$tokenLikeName instanceof ClassLikeName || ($configuration->ignoreUncoveredInternalClasses() && isset(PhpStormStubsMap::CLASSES[$tokenLikeName->toString()]));
    }
}
