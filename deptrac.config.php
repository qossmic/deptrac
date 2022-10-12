<?php

use Qossmic\Deptrac\Core\Layer\Collector\CollectorType;
use Qossmic\Deptrac\Supportive\Config\DeptracConfig;

return static function (DeptracConfig $config): void {
    $analyser = $config->layer('analyser');
    $analyser->collector(CollectorType::TYPE_DIRECTORY)->value('src/Core/Analyser/.*');

    $ast = $config->layer('ast');
    $ast->collector(CollectorType::TYPE_DIRECTORY)->value('src/Core/Analyser/.*');
    $ast->collector(CollectorType::TYPE_CLASS_NAME)->value('^PHPStan\\\\PhpDocParser\\\\.*')->private();
    $ast->collector(CollectorType::TYPE_CLASS_NAME)->value('^PhpParser\\\\.*')->private();
    $ast->collector(CollectorType::TYPE_CLASS_NAME)->value('^phpDocumentor\\\\Reflection\\\\.*')->private();

    $console = $config->layer('console');
    $console->collector(CollectorType::TYPE_DIRECTORY)->value('src/Supportive/Console/.*');

    $layer = $config->layer('layer');
    $layer->collector(CollectorType::TYPE_DIRECTORY)->value('src/Core/Layer.*');

    $config->ruleset($analyser)->accessesLayer($ast, $layer);
    $config->ruleset($layer)->accessesLayer($ast);
    $config->ruleset($console)->accessesLayer($analyser);
};
