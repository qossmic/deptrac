<?php

namespace Qossmic\Deptrac\Contract\Config\Collector;

use Qossmic\Deptrac\Contract\Config\CollectorConfig;
use Qossmic\Deptrac\Contract\Config\CollectorType;

final class BoolConfig extends CollectorConfig
{
    /** @var array<CollectorConfig> */
    private array $mustNot = [];

    /** @var array<CollectorConfig> */
    private array $must = [];

    public static function public(): self
    {
        return new self(collectorType: CollectorType::TYPE_BOOL, private: false);
    }

    public static function private(): self
    {
        return new self(collectorType: CollectorType::TYPE_BOOL, private: true);
    }

    public function withMustNot(CollectorConfig $CollectorConfig): self
    {
        $this->mustNot[] = $CollectorConfig;

        return $this;
    }

    public function withMust(CollectorConfig $CollectorConfig): self
    {
        $this->must[] = $CollectorConfig;

        return $this;
    }

    /** @return array{
     *     must: array<array-key, array{private: bool, type: string}>|mixed,
     *     must_not: array<array-key, array{private: bool, type: string}>|mixed,
     *     private: bool,
     *     type: string}
     */
    public function toArray(): array
    {
        return parent::toArray() + [
            'must_not' => array_map(static fn (CollectorConfig $v) => $v->toArray(), $this->mustNot),
            'must' => array_map(static fn (CollectorConfig $v) => $v->toArray(), $this->must),
        ];
    }
}
