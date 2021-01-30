<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

use Qossmic\Deptrac\Configuration\Exception\FileCannotBeParsedAsYamlException;
use Qossmic\Deptrac\Configuration\Exception\ParsedYamlIsNotAnArrayException;
use Qossmic\Deptrac\Configuration\Loader\YmlFileLoader;
use Qossmic\Deptrac\File\CouldNotReadFileException;

class Loader
{
    /** @var YmlFileLoader */
    private $fileLoader;

    public function __construct(YmlFileLoader $fileLoader)
    {
        $this->fileLoader = $fileLoader;
    }

    /**
     * @throws CouldNotReadFileException
     * @throws FileCannotBeParsedAsYamlException
     * @throws ParsedYamlIsNotAnArrayException
     */
    public function load(string $file): Configuration
    {
        $data = $this->fileLoader->parseFile($file);

        return Configuration::fromArray($data);
    }
}
