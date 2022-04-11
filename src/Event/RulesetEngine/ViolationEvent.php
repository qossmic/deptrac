<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Event\RulesetEngine;

use Qossmic\Deptrac\RulesetEngine\Violation;
use Symfony\Contracts\EventDispatcher\Event;

class ViolationEvent extends Event
{
    private Violation $violation;
    private bool $skipped = false;

    public function __construct(Violation $violation)
    {
        $this->violation = $violation;
    }

    public function getViolation(): Violation
    {
        return $this->violation;
    }

    public function isSkipped(): bool
    {
        return $this->skipped;
    }

    public function skip(): void
    {
        $this->skipped = true;
    }

    public function unskip(): void
    {
        $this->skipped = false;
    }
}
