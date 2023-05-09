<?php

use Internal\Qossmic\Deptrac\IgnoreDependenciesOnContract;
use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use Qossmic\Deptrac\Contract\Config\Collector\BoolConfig;
use Qossmic\Deptrac\Contract\Config\Collector\ClassNameConfig;
use Qossmic\Deptrac\Contract\Config\Collector\DirectoryConfig;
use Qossmic\Deptrac\Contract\Config\DeptracConfig;
use Qossmic\Deptrac\Contract\Config\EmitterType;
use Qossmic\Deptrac\Contract\Config\Formatter\GraphvizConfig;
use Qossmic\Deptrac\Contract\Config\Layer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (DeptracConfig $config, ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(IgnoreDependenciesOnContract::class)->tag('kernel.event_listener', ['event' => ProcessEvent::class]);

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
            $analyser = Layer::withName('Analyser')
                ->collectors(
                    DirectoryConfig::create('src/Core/Analyser/.*')
                )
                ->allowedDependencies(['Layer', 'Dependency', 'Ast']),
            $ast = Layer::withName('Ast')
                ->collectors(
                    DirectoryConfig::create('src/Core/Ast/.*'),
                    ClassNameConfig::create('^PHPStan\\PhpDocParser\\.*')->private(),
                    ClassNameConfig::create('^PhpParser\\.*')->private(),
                    ClassNameConfig::create('^phpDocumentor\\Reflection\\.*')->private(),
                )
                ->allowedDependencies(['File', 'InputCollector']),
            $console = Layer::withName('Console')
                ->collectors(
                    DirectoryConfig::create('src/Supportive/Console/.*')
                )
                ->allowedDependencies([
                    'Analyser',
                    'OutputFormatter',
                    'DependencyInjection',
                    'File',
                    'Time',
                ]),
            $dependency = Layer::withName('Dependency')
                ->collectors(
                    DirectoryConfig::create('src/Core/Dependency/.*')
                )
                ->allowedDependencies(['Ast']),
            $dependencyInjection = Layer::withName('DependencyInjection')->collectors(
                DirectoryConfig::create('src/Supportive/DependencyInjection/.*')
            ),
            $contract = Layer::withName('Contract')->collectors(
                DirectoryConfig::create('src/Contract/.*')
            ),
            $inputCollector = Layer::withName('InputCollector')
                ->collectors(
                    DirectoryConfig::create('src/Core/InputCollector/.*')
                )
                ->allowedDependencies(['File']),
            $layer = Layer::withName('Layer')
                ->collectors(
                    DirectoryConfig::create('src/Core/Layer/.*')
                )
                ->allowedDependencies(['Ast']),
            $outputFormatter = Layer::withName('OutputFormatter')
                ->collectors(
                    DirectoryConfig::create('src/Supportive/OutputFormatter/.*'),
                    ClassNameConfig::create('^phpDocumentor\\GraphViz\\.*')->private(),
                )
                ->allowedDependencies(['DependencyInjection']),
            $file = Layer::withName('File')->collectors(
                DirectoryConfig::create('src/Supportive/File/.*')
            ),
            $time = Layer::withName('Time')->collectors(
                DirectoryConfig::create('src/Supportive/Time/.*')
            ),
            $supportive = Layer::withName('Supportive')->collectors(
                BoolConfig::create()
                    ->mustNot(DirectoryConfig::create('src/Supportive/.*/.*'))
                    ->must(DirectoryConfig::create('src/Supportive/.*'))
            ),
        )
        ->formatters(
            GraphvizConfig::create()
                ->pointsToGroup(true)
                ->groups('Contract', $contract)
                ->groups('Supportive', $supportive, $file)
                ->groups('Symfony', $console, $dependencyInjection, $outputFormatter)
                ->groups('Core', $analyser, $ast, $dependency, $inputCollector, $layer)
        );
};
