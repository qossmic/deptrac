<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

final class ConfigurationRuleset
{
    /** @var array<string, string[]> */
    private $layerMap;

    /**
     * @param array<string, string[]> $arr
     */
    public static function fromArray(array $arr): self
    {
        return new self($arr);
    }

    /**
     * @param array<string, string[]> $layerMap
     */
    private function __construct(array $layerMap)
    {
        $this->layerMap = $layerMap;
    }

    /**
     * @return string[]
     */
    public function getAllowedDependencies(string $layerName): array
    {
        return $this->layerMap[$layerName] ?? [];
    }
}
