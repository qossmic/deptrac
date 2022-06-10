<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Analyser\EventHandler;

use JetBrains\PHPStormStub\PhpStormStubsMap;
use Qossmic\Deptrac\Analyser\Event\ProcessEvent;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Result\Uncovered;

/**
 * @internal
 */
class UncoveredDependentHandler
{
    private bool $ignoreUncoveredInternalClasses;

    public function __construct(bool $ignoreUncoveredInternalClasses)
    {
        $this->ignoreUncoveredInternalClasses = $ignoreUncoveredInternalClasses;
    }

    public function __invoke(ProcessEvent $event): void
    {
        $dependency = $event->getDependency();
        $dependent = $dependency->getDependent();
        $dependerLayer = $event->getDependerLayer();
        $dependentLayers = $event->getDependentLayers();
        $ruleset = $event->getResult();

        if ([] !== $dependentLayers) {
            return;
        }

        if ($dependent instanceof ClassLikeToken && !$this->ignoreUncoveredInternalClass($dependent)) {
            $ruleset->add(new Uncovered($dependency, $dependerLayer));
        }

        $event->stopPropagation();
    }

    private function ignoreUncoveredInternalClass(ClassLikeToken $token): bool
    {
        if (!$this->ignoreUncoveredInternalClasses) {
            return false;
        }

        $tokenString = $token->toString();

        return isset(PhpStormStubsMap::CLASSES[$tokenString]) || 'ReturnTypeWillChange' === $tokenString;
    }
}
