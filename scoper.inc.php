<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder as IsolatedFinder;

$polyfillsBootstrap = IsolatedFinder::create()
    ->files()
    ->in(__DIR__ . '/vendor/symfony/polyfill-*')
    ->name('*.php');

return [
    'prefix' => null,                       // string|null
    'finders' => [],                        // Finder[]
    'patchers' => [],                       // callable[]
    'exclude-files' => array_map(
        static function ($file) {
            return $file->getPathName();
        },
        iterator_to_array($polyfillsBootstrap)
    ),
    'exclude-namespaces' => [
        'Qossmic\Deptrac',
        'Symfony\Polyfill',
    ],
    'expose-global-constants' => true,   // bool
    'expose-global-classes' => true,     // bool
    'expose-global-functions' => true,   // bool
];
