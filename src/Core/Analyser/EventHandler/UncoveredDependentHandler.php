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
    public function __construct(private readonly bool $ignoreUncoveredInternalClasses)
    {
    }

    public function __invoke(ProcessEvent $event): void
    {
        $dependent = $event->dependency->getDependent();
        $ruleset = $event->getResult();

        if ([] !== $event->dependentLayers) {
            return;
        }

        if ($dependent instanceof ClassLikeToken && !$this->ignoreUncoveredInternalClass($dependent)) {
            $ruleset->add(new Uncovered($event->dependency, $event->dependerLayer));
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
