<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Console\Output;
use Symfony\Component\Console\Input\InputInterface;
use function file_exists;
use function trigger_deprecation;

trait DefaultDepFileTrait
{
    protected static function getConfigFile(InputInterface $input, Output $output): string
    {
        /** @var string $configFile */
        $configFile = $input->getOption('config-file');
        if (!file_exists($configFile)) {
            $output->getStyle()->warning(<<<TEXT
                The format and default location of the depfile have changed.
                If you have already changed this, then you can ignore this warning. Otherwise please:
                  * Update your depfile (see UPGRADE.md for details).
                  * Rename the depfile to "deptrac.yaml", if you want it to load it automatically.
                  * Use the --config-file option, if you want to load a specific file instead of using the argument.
                TEXT
            );
            /** @psalm-suppress TooManyArguments,UnusedFunctionCall */
            trigger_deprecation('qossmic/deptrac', '0.19.0', 'Using "depfile.yaml" will be removed.');

            /** @var string $configFile */
            $configFile = $input->getArgument('depfile');
        }

        return $configFile;
    }
}
