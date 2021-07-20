<?php

namespace Tests\Qossmic\Deptrac;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\ClassLikeAnalyser;
use Qossmic\Deptrac\Configuration\Configuration;
use Qossmic\Deptrac\ContainerBuilder;
use Tests\Qossmic\Deptrac\Fixtures\ClassLikeAnalyser\ClassFoo;

/**
 * @covers \Qossmic\Deptrac\ClassLikeAnalyser
 */
class ClassLikeAnalyserTest extends TestCase
{
    public function testAnalyse(): void
    {
        $configuration = Configuration::fromArray([
            'paths' => [__DIR__.'/Fixtures/ClassLikeAnalyser/'],
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
        ]);

        $container = (new ContainerBuilder(__DIR__))->build();
        $analyser = $container->get(ClassLikeAnalyser::class);
        $layers = $analyser->analyse($configuration, ClassLikeName::fromFQCN(ClassFoo::class));

        self::assertSame(['LayerFoo'], $layers);
    }
}
