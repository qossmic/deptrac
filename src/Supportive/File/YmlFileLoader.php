<?php

namespace Qossmic\Deptrac\Supportive\File;

use Qossmic\Deptrac\Supportive\File\Exception\CouldNotReadFileException;
use Qossmic\Deptrac\Supportive\File\Exception\FileCannotBeParsedAsYamlException;
use Qossmic\Deptrac\Supportive\File\Exception\ParsedYamlIsNotAnArrayException;
use DEPTRAC_202401\Symfony\Component\Yaml\Exception\ParseException;
use DEPTRAC_202401\Symfony\Component\Yaml\Yaml;
/**
 * @internal
 */
class YmlFileLoader
{
    /**
     * @return array{parameters: array<string, mixed>, services: array<string, mixed>, imports?: array<string>}
     *
     * @throws FileCannotBeParsedAsYamlException
     * @throws ParsedYamlIsNotAnArrayException
     * @throws CouldNotReadFileException
     */
    public function parseFile(string $file) : array
    {
        try {
            $data = Yaml::parse(\Qossmic\Deptrac\Supportive\File\FileReader::read($file));
        } catch (ParseException $exception) {
            throw FileCannotBeParsedAsYamlException::fromFilenameAndException($file, $exception);
        }
        if (!\is_array($data)) {
            throw ParsedYamlIsNotAnArrayException::fromFilename($file);
        }
        /** @var array{parameters: array<string, mixed>, services: array<string, mixed>, imports?: array<string>} $data */
        return $data;
    }
}
