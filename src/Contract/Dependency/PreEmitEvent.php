<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Dependency;

use Symfony\Contracts\EventDispatcher\Event;

final class PreEmitEvent extends Event
{
    public function __construct(
        public readonly string $emitterName
    ) {
    }
}
