<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Configuration;

use SensioLabs\Deptrac\Configuration\Exception\BaselineFileCannotBeReadException;
use SensioLabs\Deptrac\Configuration\Exception\FileCannotBeParsedAsYamlException;
use SensioLabs\Deptrac\Configuration\Exception\FileDoesNotExistsException;
use SensioLabs\Deptrac\Configuration\Exception\ParsedYamlIsNotAnArrayException;
use SensioLabs\Deptrac\Configuration\Loader\YmlFileLoader;

class Loader
{
    /** @var YmlFileLoader */
    private $fileLoader;

    public function __construct(YmlFileLoader $fileLoader)
    {
        $this->fileLoader = $fileLoader;
    }

    /**
     * @throws FileDoesNotExistsException
     * @throws FileCannotBeParsedAsYamlException
     * @throws ParsedYamlIsNotAnArrayException
     * @throws BaselineFileCannotBeReadException
     */
    public function load(string $file): Configuration
    {
        $data = $this->fileLoader->parseFile($file);

        return Configuration::fromArray($data);
    }
}
