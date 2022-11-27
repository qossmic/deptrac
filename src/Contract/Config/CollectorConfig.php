<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Config;

abstract class CollectorConfig
{
    protected function __construct(
        protected CollectorType $collectorType = CollectorType::TYPE_BOOL,
        protected bool $private = false,
    ) {
    }

    /**
     * @return array{'type': string, 'private': bool}
     */
    public function toArray(): array
    {
        return [
            'private' => $this->private,
            'type' => $this->collectorType->value,
        ];
    }
}
