<?php

namespace Qossmic\Deptrac\Configuration\Loader;

use Qossmic\Deptrac\Exception\Configuration\FileCannotBeParsedAsYamlException;
use Qossmic\Deptrac\Exception\Configuration\ParsedYamlIsNotAnArrayException;
use Qossmic\Deptrac\File\FileReader;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YmlFileLoader
{
    public function parseFile(string $file): array
    {
        try {
            $data = Yaml::parse(FileReader::read($file));
        } catch (ParseException $exception) {
            throw FileCannotBeParsedAsYamlException::fromFilenameAndException($file, $exception);
        }

        if (!is_array($data)) {
            throw ParsedYamlIsNotAnArrayException::fromFilename($file);
        }

        return $data;
    }
}
