<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\OutputFormatter\Configuration;

class FormatterConfiguration
{
    /** @var array<string, array<mixed>> */
    private $config;

    /**
     * @param array<string, array<mixed>> $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array<mixed>
     */
    public function getConfigFor(string $area): array
    {
        return $this->config[$area] ?? [];
    }
}
