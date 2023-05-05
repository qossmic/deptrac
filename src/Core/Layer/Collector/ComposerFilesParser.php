<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use function count;
use function in_array;

class ComposerFilesParser
{
    /** @var array<string, mixed> */
    private array $composerFile;

    /** @var array<string, mixed> */
    private array $lockFile;

    /** @var array<string, mixed> */
    private array $lockedPackages;

    public function __construct(string $composerFile, string $lockFile)
    {
        $this->composerFile = json_decode(file_get_contents($composerFile), true);
        $this->lockFile = json_decode(file_get_contents($lockFile), true);
        $this->lockedPackages = $this->getPackagesFromLockFile();
    }

    /**
     * Returns an array of all namespaces declared by the current composer file.
     *
     * @return string[]
     */
    public function getNamespaces(bool $includeDev = false): array
    {
        return $this->extractNamespaces($this->composerFile, $includeDev);
    }

    /**
     * Returns an array of all required namespaces including deep dependencies (dependencies of dependencies).
     *
     * @return string[]
     */
    public function getDeepRequirementNamespaces(bool $includeDev): array
    {
        $required = $this->getDirectDependencies($includeDev);
        $required = $this->flattenDependencies($required, $includeDev);

        return $this->autoloadableNamespacesForRequirements($required, $includeDev);
    }

    /**
     * Returns an array of directly required package names.
     *
     * @return string[]
     */
    public function getDirectDependencies(bool $includeDev): array
    {
        $required = [];
        foreach (array_keys($this->composerFile['require'] ?? []) as $packageName) {
            $required[] = (string) $packageName;
        }

        if ($includeDev) {
            foreach (array_keys($this->composerFile['require-dev'] ?? []) as $packageName) {
                $required[] = (string) $packageName;
            }
        }

        return $required;
    }

    /**
     * Resolves an array of package names to an array of namespaces declared by those packages.
     *
     * @param string[] $requirements
     *
     * @return string[]
     */
    public function autoloadableNamespacesForRequirements(array $requirements, bool $includeDev): array
    {
        $namespaces = [[]];

        foreach ($requirements as $package) {
            $namespaces[] = $this->extractNamespaces($this->lockedPackages[$package], $includeDev);
        }

        return array_merge(...$namespaces);
    }

    /**
     * @param mixed[] $topLevelRequirements
     *
     * @return mixed[]
     */
    private function flattenDependencies(array $topLevelRequirements, bool $includeDev): array
    {
        $required = [];
        $toCheck = $topLevelRequirements;

        while (count($toCheck) > 0) {
            $packageName = array_pop($toCheck);
            $package = $this->lockedPackages[$packageName] ?? null;
            if (null === $package) {
                continue;
            }

            $required[] = $packageName;

            $deepRequirements = array_keys($package['require'] ?? []);
            if ($includeDev) {
                $deepRequirements = array_merge(
                    $deepRequirements,
                    array_keys($package['require-dev'] ?? [])
                );
            }

            foreach ($deepRequirements as $name) {
                if (!in_array($name, $required, true)) {
                    $toCheck[] = $name;
                }
            }
        }

        return $required;
    }

    /**
     * @return array<string, mixed>
     */
    private function getPackagesFromLockFile(): array
    {
        $lockedPackages = [];

        foreach ($this->lockFile['packages'] ?? [] as $package) {
            $lockedPackages[(string) $package['name']] = $package;
        }

        foreach ($this->lockFile['packages-dev'] ?? [] as $package) {
            $lockedPackages[(string) $package['name']] = $package;
        }

        return $lockedPackages;
    }

    /**
     * @param array<string, mixed> $package
     *
     * @return string[]
     */
    private function extractNamespaces(array $package, bool $includeDev): array
    {
        $namespaces = [];
        foreach (array_keys($package['autoload']['psr-0'] ?? []) as $namespace) {
            $namespaces[] = (string) $namespace;
        }
        foreach (array_keys($package['autoload']['psr-4'] ?? []) as $namespace) {
            $namespaces[] = (string) $namespace;
        }

        if ($includeDev) {
            foreach (array_keys($package['autoload-dev']['psr-0'] ?? []) as $namespace) {
                $namespaces[] = (string) $namespace;
            }
            foreach (array_keys($package['autoload-dev']['psr-4'] ?? []) as $namespace) {
                $namespaces[] = (string) $namespace;
            }
        }

        return $namespaces;
    }
}
