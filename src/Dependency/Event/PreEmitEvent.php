<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Dependency\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class PreEmitEvent extends Event
{
    private $emitterName;

    public function __construct(string $emitterName)
    {
        $this->emitterName = $emitterName;
    }

    public function getEmitterName(): string
    {
        return $this->emitterName;
    }
}
