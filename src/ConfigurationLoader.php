<?php 

namespace DependencyTracker;

use Symfony\Component\Yaml\Yaml;

class ConfigurationLoader
{
    private function getPathname()
    {
        return getcwd().'/depfile.yml';
    }

    public function loadConfiguration()
    {
        return Configuration::fromArray(
            Yaml::parse(file_get_contents($this->getPathname()))
        );
    }

    public function hasConfiguration()
    {
        return file_exists($this->getPathname());
    }

    public function dumpConfiguration()
    {
        if ($this->hasConfiguration()) {
            throw new \RuntimeException('Configuration already exists.');
        }

        file_put_contents(
            $this->getPathname(),
            file_get_contents(__DIR__.'/Configuration/example_configuration.yml')
        );
    }
}
