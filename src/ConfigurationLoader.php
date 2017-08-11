<?php

namespace SensioLabs\Deptrac;

use Symfony\Component\Yaml\Yaml;

class ConfigurationLoader
{
    private $configFilePathname;

    /**
     * ConfigurationLoader constructor.
     *
     * @param $configFilePathname
     */
    public function __construct($configFilePathname)
    {
        $this->configFilePathname = $configFilePathname;
    }

    /** @return Configuration */
    public function loadConfiguration()
    {
        return Configuration::fromArray(
            Yaml::parse(file_get_contents($this->configFilePathname))
        );
    }

    /** @return mixed */
    public function getConfigFilePathname()
    {
        return $this->configFilePathname;
    }

    /** @return bool */
    public function hasConfiguration()
    {
        return file_exists($this->configFilePathname);
    }

    public function dumpConfiguration()
    {
        if ($this->hasConfiguration()) {
            throw new \RuntimeException('Configuration already exists.');
        }

        copy(__DIR__.'/Configuration/example_configuration.yml', $this->configFilePathname);
    }
}
