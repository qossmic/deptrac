<?php 

namespace DependencyTracker;

use Symfony\Component\Yaml\Yaml;

class ConfigurationLoader
{
    /**
     * @return Configuration
     */
    public function load($path)
    {
        return Configuration::fromArray(
            Yaml::parse(file_get_contents($path))
        );
    }
}
