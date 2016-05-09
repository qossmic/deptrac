<?php 

namespace SensioLabs\Deptrac\ConfigurationEngine;

class FilesystemConfigurationProvider implements ConfigurationProviderInterface
{
    public function supports($filepath) {
        return file_exists($filepath);
    }

    public function provide($filepath) {
        return file_get_contents($filepath);
    }
}
