<?php

namespace Qossmic\Deptrac\Utils\File;

use Qossmic\Deptrac\Utils\File\Exception\FileCannotBeParsedAsYamlException;
use Qossmic\Deptrac\Utils\File\Exception\ParsedYamlIsNotAnArrayException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * @internal
 */
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
