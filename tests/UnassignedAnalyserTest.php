<?php

namespace Tests\Qossmic\Deptrac;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Configuration\Configuration;
use Qossmic\Deptrac\ContainerBuilder;
use Qossmic\Deptrac\UnassignedAnalyser;
use Tests\Qossmic\Deptrac\Fixtures\LayerAnalyser\ClassBar;

/**
 * @covers \Qossmic\Deptrac\UnassignedAnalyser
 */
class UnassignedAnalyserTest extends TestCase
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
                            'regex' => '.*Foo.*',
                        ],
                    ],
                ],
            ],
            'ruleset' => [],
        ]);

        $container = (new ContainerBuilder(__DIR__))->build();
        /** @var UnassignedAnalyser $analyser */
        $analyser = $container->get(UnassignedAnalyser::class);
        $classLikes = $analyser->analyse($configuration);

        self::assertSame(
            [
                '/' . basename(__DIR__).'/Fixtures/LayerAnalyser/ClassBar.php',
                '/' . basename(__DIR__).'/Fixtures/LayerAnalyser/ClassFoo.php',
                ClassBar::class,
            ],
            $classLikes
        );
    }
}
