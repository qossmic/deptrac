<?php

namespace Tests\Qossmic\Deptrac;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Configuration\Configuration;
use Qossmic\Deptrac\ContainerBuilder;
use Qossmic\Deptrac\LayerAnalyser;
use Tests\Qossmic\Deptrac\Fixtures\LayerAnalyser\ClassBar;
use Tests\Qossmic\Deptrac\Fixtures\LayerAnalyser\ClassFoo;

/**
 * @covers \Qossmic\Deptrac\LayerAnalyser
 */
class LayerAnalyserTest extends TestCase
{
    public function testAnalyse(): void
    {
        $configuration = Configuration::fromArray([
            'paths' => [__DIR__.'/Fixtures/LayerAnalyser/'],
            'layers' => [
                [
                    'name' => 'LayerFoo',
                    'collectors' => [
                        [
                            'type' => 'className',
                            'regex' => '.*Class.*',
                        ],
                    ],
                ],
            ],
            'ruleset' => [],
        ]);

        $container = (new ContainerBuilder(__DIR__))->build();
        $analyser = $container->get(LayerAnalyser::class);
        $classLikes = $analyser->analyse($configuration, 'LayerFoo');

        self::assertSame(
            [
                ClassBar::class,
                ClassFoo::class,
            ],
            $classLikes
        );
    }
}
