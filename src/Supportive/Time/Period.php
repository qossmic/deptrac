<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Time;

use function hrtime;

final class Period
{
    private function __construct(
        private readonly float|int $startedAt,
        private readonly float|int $endedAt,
    ) {
    }

    public static function stop(StartedPeriod $startedPeriod): self
    {
        return new self(
            $startedPeriod->startedAt(),
            hrtime(true),
        );
    }

    public function toSeconds(): float
    {
        $duration = $this->endedAt - $this->startedAt;

        return $duration / 1e9;
    }
}
