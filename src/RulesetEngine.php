<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use InvalidArgumentException;
use JetBrains\PHPStormStub\PhpStormStubsMap;
use Psr\EventDispatcher\EventDispatcherInterface;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\Configuration\ConfigurationRuleset;
use Qossmic\Deptrac\Dependency\Result;
use Qossmic\Deptrac\Event\RulesetEngine\PostRulesetProcessingEvent;
use Qossmic\Deptrac\Event\RulesetEngine\ViolationEvent;
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
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function process(
        Result $dependencyResult,
        TokenLayerResolverInterface $tokenLayerResolver,
        ConfigurationRuleset $configurationRuleset
    ): Context {
        $rules = [];
        $warnings = [];
        $errors = [];

        $skippedViolationHelper = new SkippedViolationHelper($configurationRuleset->getSkipViolations());

        foreach ($dependencyResult->getDependenciesAndInheritDependencies() as $dependency) {
            $dependant = $dependency->getDependant();
            $dependantLayerNames = $tokenLayerResolver->getLayersByTokenName($dependant);

            if (!isset($warnings[$dependant->toString()]) && count($dependantLayerNames) > 1) {
                $warnings[$dependant->toString()] = Warning::tokenIsInMoreThanOneLayer($dependant, $dependantLayerNames);
            }

            foreach ($dependantLayerNames as $dependantLayerName) {
                try {
                    $allowedDependencies = $configurationRuleset->getAllowedDependencies($dependantLayerName);
                } catch (InvalidArgumentException $exception) {
                    $errors[] = new Error($exception->getMessage());
                    continue;
                }

                $dependee = $dependency->getDependee();
                $dependeeLayerNames = $tokenLayerResolver->getLayersByTokenName($dependee);

                if (0 === count($dependeeLayerNames)) {
                    if ($dependee instanceof ClassLikeName && !$this->ignoreUncoveredInternalClass($configurationRuleset, $dependee)) {
                        $rules[] = new Uncovered($dependency, $dependantLayerName);
                    }
                    continue;
                }

                foreach ($dependeeLayerNames as $dependeeLayerName) {
                    if ($dependantLayerName === $dependeeLayerName) {
                        continue;
                    }

                    if (in_array($dependeeLayerName, $allowedDependencies, true)) {
                        $rules[] = new Allowed($dependency, $dependantLayerName, $dependeeLayerName);
                        continue;
                    }

                    if ($skippedViolationHelper->isViolationSkipped($dependant->toString(), $dependee->toString())) {
                        $rules[] = new SkippedViolation($dependency, $dependantLayerName, $dependeeLayerName);
                        continue;
                    }

                    $violation = new Violation($dependency, $dependantLayerName, $dependeeLayerName);
                    $event = new ViolationEvent($violation);
                    $this->eventDispatcher->dispatch($event);

                    if ($event->isSkipped()) {
                        $rules[] = new SkippedViolation($dependency, $dependantLayerName, $dependeeLayerName);
                        continue;
                    }

                    $rules[] = $violation;
                }
            }
        }

        foreach ($skippedViolationHelper->unmatchedSkippedViolations() as $classLikeNameA => $classLikes) {
            foreach ($classLikes as $classLikeNameB) {
                $errors[] = new Error(sprintf('Skipped violation "%s" for "%s" was not matched.', $classLikeNameB, $classLikeNameA));
            }
        }

        $context = new Context($rules, $errors, $warnings);
        $event = new PostRulesetProcessingEvent($context);

        $this->eventDispatcher->dispatch($event);

        return $event->getContext();
    }

    private function ignoreUncoveredInternalClass(ConfigurationRuleset $configuration, ClassLikeName $tokenName): bool
    {
        if (!$configuration->ignoreUncoveredInternalClasses()) {
            return false;
        }

        $tokenString = $tokenName->toString();

        return isset(PhpStormStubsMap::CLASSES[$tokenString]) || 'ReturnTypeWillChange' === $tokenString;
    }
}
