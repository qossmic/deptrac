<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;

trait DefaultDepFileTrait
{
    protected function getDefaultFile(SymfonyOutput $output): string
    {
        $oldDefaultFile = getcwd().'/depfile.yml';

        if (is_file($oldDefaultFile)) {
            $output->writeLineFormatted([
                '',
                '⚠️  Old default file detected. ⚠️',
                '   The default file changed from <fg=cyan>depfile.yml</> to <fg=cyan>depfile.yaml</>.',
                '   You are getting this message because you are using deptrac without the file argument and the old default file.',
                '   Deptrac loads for now the old file. Please update your file extension from <fg=cyan>yml</> to <fg=cyan>yaml</>.',
                '',
            ]);

            return $oldDefaultFile;
        }

        return getcwd().'/depfile.yaml';
    }
}
