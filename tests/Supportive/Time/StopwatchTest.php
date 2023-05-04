<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Supportive\Time;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Supportive\Time\Stopwatch;
use Qossmic\Deptrac\Supportive\Time\StopwatchException;

class StopwatchTest extends TestCase
{
    private readonly Stopwatch $stopwatch;

    protected function setUp(): void
    {
        $this->stopwatch = new Stopwatch();
    }

    public function testEventCanNotBeStartedTwice(): void
    {
        $this->expectException(StopwatchException::class);
        $this->expectExceptionMessage('Period "test" is already started');

        $this->stopwatch->start('test');
        $this->stopwatch->start('test');
    }

    public function testEventCanNotBeStoppedWithoutBeingStarted(): void
    {
        $this->expectException(StopwatchException::class);
        $this->expectExceptionMessage('Period "test" is not started');

        $this->stopwatch->stop('test');
    }

    public function testEventFlowAndEventCanBeStartedAgain(): void
    {
        $this->stopwatch->start('test');
        $this->stopwatch->stop('test');
        $this->stopwatch->start('test');

        $this->expectNotToPerformAssertions();
    }
}
