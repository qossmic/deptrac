<?php

use Internal\Qossmic\Deptrac\IgnoreDependenciesOnContract;
use Internal\Qossmic\Deptrac\IgnoreDependenciesOnShouldNotHappenException;
use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use Qossmic\Deptrac\Contract\Config\Collector\BoolConfig;
use Qossmic\Deptrac\Contract\Config\Collector\ClassNameConfig;
use Qossmic\Deptrac\Contract\Config\Collector\DirectoryConfig;
use Qossmic\Deptrac\Contract\Config\DeptracConfig;
use Qossmic\Deptrac\Contract\Config\EmitterType;
use Qossmic\Deptrac\Contract\Config\Formatter\GraphvizConfig;
use Qossmic\Deptrac\Contract\Config\Layer;
use Qossmic\Deptrac\Contract\Config\RulesetConfig;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Qossmic\Deptrac\Contract\Config\regex;

return static function (DeptracConfig $config, ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(IgnoreDependenciesOnContract::class)->tag('kernel.event_listener', ['event' => ProcessEvent::class]);
    $services->set(IgnoreDependenciesOnShouldNotHappenException::class)->tag(
        'kernel.event_listener',
        ['event' => ProcessEvent::class]
    );

    $config
        ->paths('src')
        ->analysers(
            EmitterType::CLASS_TOKEN,
            EmitterType::CLASS_SUPERGLOBAL_TOKEN,
            EmitterType::FILE_TOKEN,
            EmitterType::FUNCTION_TOKEN,
            EmitterType::FUNCTION_SUPERGLOBAL_TOKEN,
            EmitterType::FUNCTION_CALL,
        )
        ->layers(
            $analyser = Layer::withName('Analyser')->collectors(
                DirectoryConfig::public('src/Core/Analyser/.*')
            ),
            $ast = Layer::withName('Ast')->collectors(
                DirectoryConfig::public('src/Core/Ast/.*'),
                ClassNameConfig::private(regex('^PHPStan\\PhpDocParser\\.*')),
                ClassNameConfig::private(regex('^PhpParser\\.*')),
                ClassNameConfig::private(regex('^phpDocumentor\\Reflection\\.*'))
            ),
            $console = Layer::withName('Console')->collectors(
                DirectoryConfig::public('src/Supportive/Console/.*')
            ),
            $dependency = Layer::withName('Dependency')->collectors(
                DirectoryConfig::public('src/Core/Dependency/.*')
            ),
            $dependencyInjection = Layer::withName('DependencyInjection')->collectors(
                DirectoryConfig::public('src/Supportive/DependencyInjection/.*')
            ),
            $contract = Layer::withName('Contract')->collectors(
                DirectoryConfig::public('src/Contract/.*')
            ),
            $inputCollector = Layer::withName('InputCollector')->collectors(
                DirectoryConfig::public('src/Core/InputCollector/.*')
            ),
            $layer = Layer::withName('Layer')->collectors(
                DirectoryConfig::public('src/Core/Layer/.*')
            ),
            $outputFormatter = Layer::withName('OutputFormatter')->collectors(
                DirectoryConfig::public('src/Supportive/OutputFormatter/.*'),
                ClassNameConfig::private(regex('^phpDocumentor\\GraphViz\\.*')),
            ),
            $file = Layer::withName('File')->collectors(
                DirectoryConfig::public('src/Supportive/File/.*')
            ),
            $supportive = Layer::withName('Supportive')->collectors(
                BoolConfig::public()
                    ->withMustNot(DirectoryConfig::public('src/Supportive/.*/.*'))
                    ->withMust(DirectoryConfig::public('src/Supportive/.*'))
            ),
        )
        ->rulesets(
            RulesetConfig::layer($layer)->accesses($ast),
            RulesetConfig::layer($console)->accesses($analyser, $outputFormatter, $dependencyInjection, $file),
            RulesetConfig::layer($dependency)->accesses($ast),
            RulesetConfig::layer($analyser)->accesses($layer, $dependency, $ast),
            RulesetConfig::layer($outputFormatter)->accesses($console, $dependencyInjection),
            RulesetConfig::layer($ast)->accesses($file, $inputCollector),
            RulesetConfig::layer($inputCollector)->accesses($file),
            RulesetConfig::layer($supportive)->accesses($file),
            RulesetConfig::layer($contract),
        )
        ->formatters(
            GraphvizConfig::create()
                ->pointsToGroup(true)
                ->groups('Contract', $contract)
                ->groups('Supportive', $supportive, $file)
                ->groups('Symfony', $console, $dependencyInjection, $outputFormatter)
                ->groups('Core', $analyser, $ast, $dependency, $inputCollector, $layer)
        )
    ;
};
