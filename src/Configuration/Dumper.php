<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

use Qossmic\Deptrac\Exception\Configuration\FileAlreadyExistsException;

class Dumper
{
    /**
     * @throws FileAlreadyExistsException
     */
    public function dump(string $file): void
    {
        if (file_exists($file)) {
            throw new FileAlreadyExistsException();
        }

        copy(__DIR__.'/example_configuration.yml', $file);
    }
}
