<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Configuration;

use SensioLabs\Deptrac\Configuration\Exception\FileCannotBeParsedAsYamlException;
use SensioLabs\Deptrac\Configuration\Exception\MissingFileException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Loader
{
    /**
     * @throws MissingFileException
     * @throws FileCannotBeParsedAsYamlException
     */
    public function load(string $file): Configuration
    {
        if (!file_exists($file)) {
            throw new MissingFileException();
        }

        try {
            $data = Yaml::parse(file_get_contents($file));
        } catch (ParseException $exception) {
            throw FileCannotBeParsedAsYamlException::fromFilename($file);
        }

        return Configuration::fromArray($data);
    }
}
