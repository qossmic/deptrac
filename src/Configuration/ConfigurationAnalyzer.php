<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

final class ConfigurationAnalyzer
{
    /** @var array<string, mixed> */
    private $config;

    /**
     * @param array<string, mixed> $arr
     */
    public static function fromArray(array $arr): self
    {
        return new self($arr);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function __construct(array $config)
    {
        $this->config = $config;
    }

    public function isCountingUseStatements(): bool
    {
        return (bool) ($this->config['count_use_statements'] ?? true);
    }
}
