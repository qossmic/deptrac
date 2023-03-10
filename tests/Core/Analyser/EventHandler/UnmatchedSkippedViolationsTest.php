<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Analyser\EventHandler;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Analyser\PostProcessEvent;
use Qossmic\Deptrac\Core\Analyser\EventHandler\UnmatchedSkippedViolations;

class UnmatchedSkippedViolationsTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = UnmatchedSkippedViolations::getSubscribedEvents();

        self::assertCount(1, $subscribedEvents);
        self::assertArrayHasKey(PostProcessEvent::class, $subscribedEvents);
        self::assertSame(['handleUnmatchedSkipped'], $subscribedEvents[PostProcessEvent::class]);
    }
}
