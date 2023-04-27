<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Supportive\Time;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Supportive\Time\StartedPeriod;

class PeriodTest extends TestCase
{
    public function testPeriodCanBeConvertedToSeconds(): void
    {
        $period = StartedPeriod::start()->stop();

        self::assertEqualsWithDelta(0, $period->toSeconds(), 1.0);
    }
}
