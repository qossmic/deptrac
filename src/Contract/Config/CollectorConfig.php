<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Config;

abstract class CollectorConfig
{
    protected function __construct(
        protected readonly CollectorType $collectorType = CollectorType::TYPE_BOOL,
        protected readonly bool $private = false,
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
