<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Analyser\EventHandler;

use Qossmic\Deptrac\Contract\Analyser\EventHelper;
use Qossmic\Deptrac\Contract\Analyser\PostProcessEvent;
use Qossmic\Deptrac\Contract\Result\Error;
use DEPTRAC_202401\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function sprintf;
/**
 * @internal
 */
class UnmatchedSkippedViolations implements EventSubscriberInterface
{
    public function __construct(private readonly EventHelper $eventHelper)
    {
    }
    public function handleUnmatchedSkipped(PostProcessEvent $event) : void
    {
        $ruleset = $event->getResult();
        foreach ($this->eventHelper->unmatchedSkippedViolations() as $tokenA => $tokensB) {
            foreach ($tokensB as $tokenB) {
                $ruleset->addError(new Error(sprintf('Skipped violation "%s" for "%s" was not matched.', $tokenB, $tokenA)));
            }
        }
    }
    public static function getSubscribedEvents()
    {
        return [PostProcessEvent::class => ['handleUnmatchedSkipped']];
    }
}
