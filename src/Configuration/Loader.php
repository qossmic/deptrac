<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Configuration;

use SensioLabs\Deptrac\Configuration\Exception\MissingFileException;
use Symfony\Component\Yaml\Yaml;

class Loader
{
    /**
     * @throws MissingFileException
     */
    public function load(string $file): Configuration
    {
        if (!file_exists($file)) {
            throw new MissingFileException();
        }

        return Configuration::fromArray(
            Yaml::parse(file_get_contents($file))
        );
    }
}
