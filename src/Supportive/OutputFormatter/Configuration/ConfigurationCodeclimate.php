<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\OutputFormatter\Configuration;

final class ConfigurationCodeclimate
{
    /** @var array{failure?: string, skipped?: string, uncovered?: string} */
    private array $severityMap;

    /**
     * @param array{severity?: array{failure?: string, skipped?: string, uncovered?: string}} $array
     */
    public static function fromArray(array $array): self
    {
        return new self($array['severity'] ?? []);
    }

    /**
     * @param array{failure?: string, skipped?: string, uncovered?: string} $severityMap
     */
    private function __construct(array $severityMap)
    {
        $this->severityMap = $severityMap;
    }

    public function getSeverity(string $key): ?string
    {
        return $this->severityMap[$key] ?? null;
    }
}
