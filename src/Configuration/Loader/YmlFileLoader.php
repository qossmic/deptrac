<?php

namespace SensioLabs\Deptrac\Configuration\Loader;

use SensioLabs\Deptrac\Configuration\Exception\BaselineFileCannotBeReadException;
use SensioLabs\Deptrac\Configuration\Exception\FileCannotBeParsedAsYamlException;
use SensioLabs\Deptrac\Configuration\Exception\FileCannotBeReadException;
use SensioLabs\Deptrac\Configuration\Exception\FileDoesNotExistsException;
use SensioLabs\Deptrac\Configuration\Exception\ParsedYamlIsNotAnArrayException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YmlFileLoader
{
    public function parseFile(string $file): array
    {
        if (!is_file($file)) {
            throw FileDoesNotExistsException::fromFilename($file);
        }

        if (false === ($content = file_get_contents($file))) {
            throw FileCannotBeReadException::fromFilename($file);
        }

        try {
            $data = Yaml::parse($content);
            if (isset($data['baseline'])) {
                $pathPrefix = dirname($file);
                $importFile = $pathPrefix.DIRECTORY_SEPARATOR.$data['baseline'];
                if (false === file_exists($importFile) || false === ($importContent = file_get_contents($importFile))) {
                    throw BaselineFileCannotBeReadException::fromFilename($importFile);
                }
                $data = array_merge_recursive(
                    $data,
                    Yaml::parse($importContent)
                );

                unset($data['baseline']);
            }
        } catch (ParseException $exception) {
            throw FileCannotBeParsedAsYamlException::fromFilename($file);
        }

        if (!is_array($data)) {
            throw ParsedYamlIsNotAnArrayException::fromFilename($file);
        }

        return $data;
    }
}
