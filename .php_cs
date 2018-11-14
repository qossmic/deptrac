<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src');

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'no_superfluous_phpdoc_tags' => true,
        'no_empty_phpdoc' => true,
        'no_empty_comment' => true,
        'strict_param' => true,
    ])
    ->setFinder($finder);
