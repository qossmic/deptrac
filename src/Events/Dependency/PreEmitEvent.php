<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Events\Dependency;

use Symfony\Contracts\EventDispatcher\Event;

final class PreEmitEvent extends Event
{
    private string $emitterName;

    public function __construct(string $emitterName)
    {
        $this->emitterName = $emitterName;
    }

    public function getEmitterName(): string
    {
        return $this->emitterName;
    }
}
