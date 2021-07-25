<?php

namespace Tests\Qossmic\Deptrac;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileName;
use Qossmic\Deptrac\AstRunner\AstMap\FunctionName;
use Qossmic\Deptrac\Configuration\Configuration;;
use Qossmic\Deptrac\ContainerBuilder;
use Qossmic\Deptrac\TokenAnalyser;
use Tests\Qossmic\Deptrac\Fixtures\TokenAnalyser\ClassFoo;

/**
 * @covers \Qossmic\Deptrac\TokenAnalyser
 */
class TokenAnalyserTest extends TestCase
{
    public function testAnalyseClass(): void
    {
        $configuration = Configuration::fromArray(
            [
                'paths' => [__DIR__.'/Fixtures/TokenAnalyser/'],
                'layers' => [
                    [
                        'name' => 'LayerFoo',
                        'collectors' => [
                            [
                                'type' => 'className',
                                'regex' => '.*ClassFoo',
                            ],
                        ],
                    ],
                ],
                'ruleset' => [],
            ]
        );

        $container = (new ContainerBuilder(__DIR__))->build();
        $analyser = $container->get(TokenAnalyser::class);
        $layers = $analyser->analyse($configuration, ClassLikeName::fromFQCN(ClassFoo::class));

        self::assertSame(['LayerFoo'], $layers);
    }

    public function testAnalyseFunction(): void
    {
        $configuration = Configuration::fromArray(
            [
                'paths' => [__DIR__.'/Fixtures/TokenAnalyser/'],
                'layers' => [
                    [
                        'name' => 'LayerFoo',
                        'collectors' => [
                            [
                                'type' => 'functionName',
                                'regex' => '.*FunctionFoo',
                            ],
                        ],
                    ],
                ],
                'ruleset' => [],
            ]
        );

        $container = (new ContainerBuilder(__DIR__))->build();
        $analyser = $container->get(TokenAnalyser::class);
        $layers = $analyser->analyse($configuration, FunctionName::fromFQCN('Tests\Qossmic\Deptrac\Fixtures\TokenAnalyser\FunctionFoo'));

        self::assertSame(['LayerFoo'], $layers);
    }

    public function testAnalyseFile(): void
    {
        $configuration = Configuration::fromArray(
            [
                'paths' => [__DIR__.'/Fixtures/TokenAnalyser/'],
                'layers' => [
                    [
                        'name' => 'LayerFoo',
                        'collectors' => [
                            [
                                'type' => 'directory',
                                'regex' => '.*/Fixtures/TokenAnalyser/',
                            ],
                        ],
                    ],
                ],
                'ruleset' => [],
            ]
        );

        $container = (new ContainerBuilder(__DIR__))->build();
        $analyser = $container->get(TokenAnalyser::class);
        $layers = $analyser->analyse($configuration, new FileName('/tests/Fixtures/TokenAnalyser/ClassFoo.php'));

        self::assertSame(['LayerFoo'], $layers);
    }
}
