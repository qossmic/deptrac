<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder as IsolatedFinder;

$polyfillsBootstrap = IsolatedFinder::create()
    ->files()
    ->in(__DIR__.'/vendor/symfony/polyfill-*')
    ->name('*.php');

return [
    'prefix' => 'DEPTRAC_'.time(),
    'finders' => [],
    'patchers' => [],
    'output-dir' => './deptrac-build/',
    'tag-declarations-as-internal' => false,
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
    'expose-functions' => ['trigger_deprecation'],
    'expose-global-constants' => false,
    'expose-global-classes' => false,
    'expose-global-functions' => false,
];
