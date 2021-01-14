<?php

namespace SensioLabs\Deptrac\Configuration\Loader;

use SensioLabs\Deptrac\Configuration\Exception\FileCannotBeParsedAsYamlException;
use SensioLabs\Deptrac\Configuration\Exception\ParsedYamlIsNotAnArrayException;
use SensioLabs\Deptrac\File\FileReader;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YmlFileLoader
{
    public function parseFile(string $file): array
    {
        try {
            $data = Yaml::parse(FileReader::read($file));
        } catch (ParseException $exception) {
            throw FileCannotBeParsedAsYamlException::fromFilename($file);
        }

        if (!is_array($data)) {
            throw ParsedYamlIsNotAnArrayException::fromFilename($file);
        }

        if (isset($data['baseline'])) {
            $data = $this->importBaseline($file, $data);
        }

        return $data;
    }

    private function importBaseline(string $file, array $data): array
    {
        $pathPrefix = dirname($file);
        $importFile = $pathPrefix.DIRECTORY_SEPARATOR.$data['baseline'];

        try {
            $baselineData = Yaml::parse(FileReader::read($importFile));
        } catch (ParseException $e) {
            throw FileCannotBeParsedAsYamlException::fromFilename($importFile);
        }

        if (!is_array($baselineData)) {
            throw ParsedYamlIsNotAnArrayException::fromFilename($importFile);
        }

        $data = array_merge_recursive($data, $baselineData);
        unset($data['baseline']);

        return $data;
    }
}
