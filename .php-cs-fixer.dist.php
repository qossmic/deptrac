<?php

$finder = (new PhpCsFixer\Finder())
    ->in([__DIR__.'/config', __DIR__.'/src', __DIR__.'/tests'])
    ->exclude('Fixtures')
    ->append([__DIR__.'/deptrac.php']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'self_static_accessor' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true],
        'no_unused_imports' => true,
        'static_lambda' => true,
        'strict_param' => true,
        'phpdoc_to_comment' => false,
    ])
    ->setFinder($finder);
