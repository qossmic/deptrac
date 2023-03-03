<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DowngradePhp74\Rector\ClassMethod\DowngradeCovariantReturnTypeRector;
use Rector\Set\ValueObject\DowngradeLevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->parallel();

    $rectorConfig->sets([DowngradeLevelSetList::DOWN_TO_PHP_72]);

    $rectorConfig->skip([
        '*/tests/*',
        # missing "optional" dependency and never used here
        '*/symfony/framework-bundle/KernelBrowser.php',

        // skip for parent type override, see https://github.com/symplify/symplify/issues/4500
        DowngradeCovariantReturnTypeRector::class => [
                                'doctrine/annotations/lib/Doctrine/Common/Annotations/DocLexer.php',
            ],
        ]);
};
