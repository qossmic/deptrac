<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser\EventHandler;

use JetBrains\PHPStormStub\PhpStormStubsMap;
use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use Qossmic\Deptrac\Contract\Result\Uncovered;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;

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
