<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Configuration;

use SensioLabs\Deptrac\Configuration\Exception\FileCannotBeParsedAsYamlException;
use SensioLabs\Deptrac\Configuration\Exception\FileCannotBeReadException;
use SensioLabs\Deptrac\Configuration\Exception\FileDoesNotExistsException;
use SensioLabs\Deptrac\Configuration\Exception\ParsedYamlIsNotAnArrayException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Loader
{
    /**
     * @throws FileDoesNotExistsException
     * @throws FileCannotBeParsedAsYamlException
     * @throws ParsedYamlIsNotAnArrayException
     */
    public function load(string $file): Configuration
    {
        if (!is_file($file)) {
            throw FileDoesNotExistsException::fromFilename($file);
        }

        if (false === ($content = file_get_contents($file))) {
            throw FileCannotBeReadException::fromFilename($file);
        }

        try {
            $data = Yaml::parse($content);
        } catch (ParseException $exception) {
            throw FileCannotBeParsedAsYamlException::fromFilename($file);
        }

        if (!is_array($data)) {
            throw ParsedYamlIsNotAnArrayException::fromFilename($file);
        }

        return Configuration::fromArray($data);
    }
}
