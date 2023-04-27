<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Supportive\Time;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Supportive\Time\StartedPeriod;

use function hrtime;

class StartedPeriodTest extends TestCase
{
    public function testPeriodCanBeStartedAndStopped(): void
    {
        $now = (float) hrtime(true);

        $period = StartedPeriod::start();
        $period->stop();

        self::assertEqualsWithDelta($now, $period->startedAt(), 1000000.0);
    }
}
