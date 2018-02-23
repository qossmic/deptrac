<?php

namespace SensioLabs\Deptrac;

use Symfony\Component\Yaml\Yaml;

class ConfigurationLoader
{
    private $configFilePathname;

    public function __construct(string $configFilePathname)
    {
        $this->configFilePathname = $configFilePathname;
    }

    public function loadConfiguration(): Configuration
    {
        return Configuration::fromArray(
            Yaml::parse(file_get_contents($this->configFilePathname))
        );
    }

    public function getConfigFilePathname(): string
    {
        return $this->configFilePathname;
    }

    public function hasConfiguration(): bool
    {
        return file_exists($this->configFilePathname);
    }

    public function dumpConfiguration()
    {
        if ($this->hasConfiguration()) {
            throw new \RuntimeException('Configuration already exists.');
        }

        file_put_contents(
            $this->configFilePathname,
            file_get_contents(__DIR__.'/Configuration/example_configuration.yml')
        );
    }
}
