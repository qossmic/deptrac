<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Config;

use Qossmic\Deptrac\Core\Layer\Collector\CollectorType;

final class CollectorConfig
{
    private ?string $value = null;
    private bool $private = false;

    private function __construct(
        private readonly CollectorType $collectorType
    ) {
    }

    public static function fromType(CollectorType $collectorType): self
    {
        $new = new self($collectorType);

        return $new;
    }

    public function value(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function private(): self
    {
        $this->private = true;

        return $this;
    }

    public function toArray(): array
    {
        return ['type' => $this->collectorType->value, 'value' => $this->value];
    }
}
