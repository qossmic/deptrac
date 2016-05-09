<?php

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\ConfigurationEngine\ConfigurationEngineInterface;

class ConfigurationLoader
{
    private $configFilePathname;

    /** @var ConfigurationEngineInterface */
    private $configurtionEngine;

    /**
     * ConfigurationLoader constructor.
     *
     * @param $configFilePathname
     */
    public function __construct(
        $configFilePathname,
        ConfigurationEngineInterface $configurationEngine
    )
    {
        $this->configFilePathname = $configFilePathname;
        $this->configurtionEngine = $configurationEngine;
    }

    /** @return Configuration */
    public function loadConfiguration()
    {
        return Configuration::fromArray(
            $this->configurtionEngine->render($this->configFilePathname)
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

        file_put_contents(
            $this->configFilePathname,
            file_get_contents(__DIR__.'/Configuration/example_configuration.yml')
        );
    }
}
