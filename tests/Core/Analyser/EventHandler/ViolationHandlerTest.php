<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Analyser\EventHandler;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Analyser\EventHelper;
use Qossmic\Deptrac\Contract\Analyser\PostProcessEvent;
use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;

class ViolationHandlerTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = EventHelper::getSubscribedEvents();

        self::assertCount(2, $subscribedEvents);
        self::assertArrayHasKey(ProcessEvent::class, $subscribedEvents);
        self::assertSame(['handleViolation', -32], $subscribedEvents[ProcessEvent::class]);
        self::assertArrayHasKey(PostProcessEvent::class, $subscribedEvents);
        self::assertSame(['handleUnmatchedSkipped'], $subscribedEvents[PostProcessEvent::class]);
    }
}
