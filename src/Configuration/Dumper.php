<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

use Qossmic\Deptrac\Configuration\Exception\FileExistsException;

class Dumper
{
    /**
     * @throws FileExistsException
     */
    public function dump(string $file): void
    {
        if (file_exists($file)) {
            throw new FileExistsException();
        }

        copy(__DIR__.'/example_configuration.yml', $file);
    }
}
