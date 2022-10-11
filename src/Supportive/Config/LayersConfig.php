<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Config;

final class LayersConfig
{
    public function __construct(
        public readonly string $name
    ) {
    }

    public function toArray(): array
    {
        return [];
    }
}
