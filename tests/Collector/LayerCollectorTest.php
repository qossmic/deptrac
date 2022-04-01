<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Collector;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;
use Qossmic\Deptrac\Collector\ClassNameCollector;
use Qossmic\Deptrac\Collector\LayerCollector;
use Qossmic\Deptrac\Collector\Registry;
use Qossmic\Deptrac\Configuration\ConfigurationLayer;

final class LayerCollectorTest extends TestCase
{
    public function testConfig(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('LayerCollector needs the layer configuration');

        (new LayerCollector())->satisfy(
            [],
            $this->createMock(AstClassReference::class),
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );
    }

    public function testResolvable(): void
    {
        $resolved = (new LayerCollector())->resolvable(
            ['layer' => 'test'],
            $this->createMock(Registry::class),
            ['test' => false, 'somethingElse' => true]
        );
        self::assertEquals(true, $resolved);
    }

    public function testUnresolvable(): void
    {
        $resolved = (new LayerCollector())->resolvable(
            ['layer' => 'test'],
            $this->createMock(Registry::class),
            ['somethingElse']
        );
        self::assertEquals(false, $resolved);
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, string $className, bool $expected): void
    {
        $configuration = array_map(
            static fn (array $config): ConfigurationLayer => ConfigurationLayer::fromArray($config),
            $configuration
        );
        $collectorRegistry = $this->createMock(Registry::class);
        $collectorRegistry->method('getCollector')
            ->willReturnMap([
                ['className', new ClassNameCollector()],
            ]);
        $resolved = (new LayerCollector())->satisfy(
            $configuration['layerCollectorLayer']->getCollectors()[0]->getArgs(),
            new AstClassReference(AstMap\ClassLikeName::fromFQCN($className)),
            $this->createMock(AstMap::class),
            $collectorRegistry,
            [$configuration['otherLayer']->getName() => $expected]
        );

        self::assertEquals($expected, $resolved);
    }

    public function dataProviderSatisfy(): iterable
    {
        yield [
            [
                'layerCollectorLayer' => [
                    'name' => 'layerCollectorLayer',
                    'collectors' => [
                        [
                            'type' => 'layer',
                            'layer' => 'otherLayer',
                        ],
                    ],
                ],
                'otherLayer' => [
                    'name' => 'otherLayer',
                    'collectors' => [
                        [
                            'type' => 'className',
                            'regex' => 'a',
                        ],
                    ],
                ],
            ],
            'foo\bar',
            true,
        ];
        yield [
            [
                'layerCollectorLayer' => [
                    'name' => 'layerCollectorLayer',
                    'collectors' => [
                        [
                            'type' => 'layer',
                            'layer' => 'otherLayer',
                        ],
                    ],
                ],
                'otherLayer' => [
                    'name' => 'otherLayer',
                    'collectors' => [
                        [
                            'type' => 'className',
                            'regex' => 'a',
                        ],
                    ],
                ],
            ],
            'foo\bbr',
            false,
        ];
    }
}
