<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Console\Output;
use Symfony\Component\Console\Input\InputInterface;
use function file_exists;
use function strlen;
use function trigger_deprecation;
use const DIRECTORY_SEPARATOR;

trait DefaultDepFileTrait
{
    protected static function getConfigFile(InputInterface $input, Output $output): string
    {
        /** @var string $configFile */
        $configFile = $input->getOption('config-file');

        $defaultConfigFile = DIRECTORY_SEPARATOR.'deptrac.yaml';
        $defaultLegacyFile = DIRECTORY_SEPARATOR.'depfile.yaml';

        $legacyFile = $defaultLegacyFile;
        if ($input->hasArgument('depfile')) {
            /** @var string $legacyFile */
            $legacyFile = $input->getArgument('depfile');
        } elseif ($input->hasOption('depfile')) {
            /** @var string $legacyFile */
            $legacyFile = $input->getOption('depfile');
        }

        if (0 !== substr_compare($configFile, $defaultConfigFile, -strlen($defaultConfigFile))
            || (file_exists($configFile)
            && 0 === substr_compare($legacyFile, $defaultLegacyFile, -strlen($defaultLegacyFile)))
        ) {
            return $configFile;
        }

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

        return $legacyFile;
    }
}
