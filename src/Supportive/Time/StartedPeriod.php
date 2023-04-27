<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Time;

final class StartedPeriod
{
    private function __construct(
        private readonly float|int $startedAt
    ) {
    }

    public static function start(): self
    {
        return new self(
            hrtime(true),
        );
    }

    public function stop(): Period
    {
        return Period::stop($this);
    }

    public function startedAt(): float
    {
        return $this->startedAt;
    }
}
