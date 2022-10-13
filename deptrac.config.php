<?php

use Qossmic\Deptrac\Core\Layer\Collector\CollectorType;
use Qossmic\Deptrac\Supportive\Config\DeptracConfig;

return static function (DeptracConfig $config): void {
    $analyser = $config->layer('analyser');
    $analyser->collector(CollectorType::TYPE_DIRECTORY)->value('src/Core/Analyser/.*');

    $ast = $config->layer('ast');
    $ast->collector(CollectorType::TYPE_DIRECTORY)->value('src/Core/Ast/.*');
    $ast->collector(CollectorType::TYPE_CLASS_NAME)->value('^PHPStan\\\\PhpDocParser\\\\.*')->private();
    $ast->collector(CollectorType::TYPE_CLASS_NAME)->value('^PhpParser\\\\.*')->private();
    $ast->collector(CollectorType::TYPE_CLASS_NAME)->value('^phpDocumentor\\\\Reflection\\\\.*')->private();

    $console = $config->layer('console');
    $console->collector(CollectorType::TYPE_DIRECTORY)->value('src/Supportive/Console/.*');

    $dependency = $config->layer('dependency');
    $dependency->collector(CollectorType::TYPE_DIRECTORY)->value('src/Core/Dependency/.*');

    $dependencyInjection = $config->layer('dependency_injection');
    $dependencyInjection->collector(CollectorType::TYPE_DIRECTORY)->value('src/Supportive/DependencyInjection/.*');

    $contract = $config->layer('contract');
    $contract->collector(CollectorType::TYPE_DIRECTORY)->value('src/Contract/.*');

    $inputCollector = $config->layer('input_collector');
    $inputCollector->collector(CollectorType::TYPE_DIRECTORY)->value('src/Core/InputCollector/.*');

    $layer = $config->layer('layer');
    $layer->collector(CollectorType::TYPE_DIRECTORY)->value('src/Core/Layer/.*');

    $outputFormatter = $config->layer('output_formatter');
    $outputFormatter->collector(CollectorType::TYPE_DIRECTORY)->value('src/Core/OutputFormatter/.*');
    $outputFormatter->collector(CollectorType::TYPE_CLASS_NAME)->value('^phpDocumentor\\\\GraphViz\\\\.*')->private();

    $file = $config->layer('file');
    $file->collector(CollectorType::TYPE_DIRECTORY)->value('src/Supportive/File/.*');

    $supportive = $config->layer('supportive');
    $supportiveCollector = $supportive->collector(CollectorType::TYPE_BOOL);
    $supportiveCollector->mustNot(CollectorType::TYPE_DIRECTORY)->value('src/Supportive/.*/.*');
    $supportiveCollector->must(CollectorType::TYPE_DIRECTORY)->value('src/Supportive/.*/.*');

    // ruleset
    $config->ruleset($layer)->accessesLayer($ast);
    $config->ruleset($console)->accessesLayer($analyser, $outputFormatter, $dependencyInjection, $file);
    $config->ruleset($dependency)->accessesLayer($ast);
    $config->ruleset($analyser)->accessesLayer($layer, $dependency, $ast);
    $config->ruleset($outputFormatter)->accessesLayer($console, $dependencyInjection);
    $config->ruleset($ast)->accessesLayer($file, $inputCollector);
    $config->ruleset($inputCollector)->accessesLayer($file);
};
