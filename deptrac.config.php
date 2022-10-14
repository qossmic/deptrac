<?php

use Amp\Emitter;
use Qossmic\Deptrac\Core\Layer\Collector\CollectorType;
use Qossmic\Deptrac\Supportive\Config\DeptracConfig;
use Qossmic\Deptrac\Supportive\DependencyInjection\EmitterType;

return static function (DeptracConfig $config): void {
    $config->paths(['./src']);
    // analyser
    $config->analyser(
        EmitterType::CLASS_TOKEN,
        EmitterType::CLASS_SUPERGLOBAL_TOKEN,
        EmitterType::FILE_TOKEN,
        EmitterType::FUNCTION_TOKEN,
        EmitterType::FUNCTION_SUPERGLOBAL_TOKEN,
        EmitterType::FUNCTION_CALL,
    );

    // layer
    $analyser = $config->layer('Analyser');
    $analyser->collector(CollectorType::TYPE_DIRECTORY)->value('src/Core/Analyser/.*');

    $ast = $config->layer('Ast');
    $ast->collector(CollectorType::TYPE_DIRECTORY)->value('src/Core/Ast/.*');
    $ast->collector(CollectorType::TYPE_CLASS_NAME)->value('^PHPStan\\\\PhpDocParser\\\\.*')->private();
    $ast->collector(CollectorType::TYPE_CLASS_NAME)->value('^PhpParser\\\\.*')->private();
    $ast->collector(CollectorType::TYPE_CLASS_NAME)->value('^phpDocumentor\\\\Reflection\\\\.*')->private();

    $console = $config->layer('Console');
    $console->collector(CollectorType::TYPE_DIRECTORY)->value('src/Supportive/Console/.*');

    $dependency = $config->layer('Dependency');
    $dependency->collector(CollectorType::TYPE_DIRECTORY)->value('src/Core/Dependency/.*');

    $dependencyInjection = $config->layer('DependencyInjection');
    $dependencyInjection->collector(CollectorType::TYPE_DIRECTORY)->value('src/Supportive/DependencyInjection/.*');

    $contract = $config->layer('Contract');
    $contract->collector(CollectorType::TYPE_DIRECTORY)->value('src/Contract/.*');

    $inputCollector = $config->layer('InputCollector');
    $inputCollector->collector(CollectorType::TYPE_DIRECTORY)->value('src/Core/InputCollector/.*');

    $layer = $config->layer('Layer');
    $layer->collector(CollectorType::TYPE_DIRECTORY)->value('src/Core/Layer/.*');

    $outputFormatter = $config->layer('OutputFormatter');
    $outputFormatter->collector(CollectorType::TYPE_DIRECTORY)->value('src/Supportive/OutputFormatter/.*');
    $outputFormatter->collector(CollectorType::TYPE_CLASS_NAME)->value('^phpDocumentor\\\\GraphViz\\\\.*')->private();

    $file = $config->layer('File');
    $file->collector(CollectorType::TYPE_DIRECTORY)->value('src/Supportive/File/.*');

    $supportive = $config->layer('Supportive');
    $supportiveCollector = $supportive->collector(CollectorType::TYPE_BOOL);
    $supportiveCollector->mustNot(CollectorType::TYPE_DIRECTORY)->value('src/Supportive/.*/.*');
    $supportiveCollector->must(CollectorType::TYPE_DIRECTORY)->value('src/Supportive/.*');

    // ruleset
    $config->ruleset($layer)->accessesLayer($ast);
    $config->ruleset($console)->accessesLayer($analyser, $outputFormatter, $dependencyInjection, $file);
    $config->ruleset($dependency)->accessesLayer($ast);
    $config->ruleset($analyser)->accessesLayer($layer, $dependency, $ast);
    $config->ruleset($outputFormatter)->accessesLayer($console, $dependencyInjection);
    $config->ruleset($ast)->accessesLayer($file, $inputCollector);
    $config->ruleset($inputCollector)->accessesLayer($file);
};
