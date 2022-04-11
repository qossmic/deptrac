<?php

namespace Qossmic\Deptrac\File;

use Qossmic\Deptrac\File\Exception\FileCannotBeParsedAsYamlException;
use Qossmic\Deptrac\File\Exception\ParsedYamlIsNotAnArrayException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YmlFileLoader
{
    /**
     * @return array{parameters: array<string, mixed>, services: array<string, mixed>, imports?: array<string>}
     */
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

        /** @var array{parameters: array<string, mixed>, services: array<string, mixed>, imports?: array<string>} $data */
        return $data;
    }
}
