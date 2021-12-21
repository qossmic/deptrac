<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\Collector\CollectorInterface;
use Qossmic\Deptrac\Collector\LayerCollector;
use Qossmic\Deptrac\Collector\Registry;
use Qossmic\Deptrac\Configuration\Configuration;
use Qossmic\Deptrac\Configuration\ConfigurationLayer;
use Qossmic\Deptrac\Configuration\ParameterResolver;
use Qossmic\Deptrac\TokenLayerResolver;

final class TokenLayerResolverTest extends TestCase
{
    private function getCollector(bool $return)
    {
        $collector = $this->createMock(CollectorInterface::class);
        $collector->method('satisfy')->with(
            $this->isType('array'),
            $this->isInstanceOf(AstClassReference::class),
            $this->isInstanceOf(AstMap::class),
            $this->isInstanceOf(Registry::class)
        )->willReturn($return);
        $collector->method('resolvable')->with(
            $this->isType('array'),
            $this->isInstanceOf(Registry::class),
            $this->isType('array')
        )->willReturn(true);

        return $collector;
    }

    public function provideGetLayersByClassLikeName(): iterable
    {
        yield [
            true,
            true,
            true,
            ['LayerA', 'LayerB'],
        ];

        yield [
            true,
            false,
            true,
            ['LayerA', 'LayerB'],
        ];

        yield [
            false,
            false,
            true,
            ['LayerB'],
        ];

        yield [
            true,
            true,
            false,
            ['LayerA', 'LayerB'],
        ];

        yield [
            true,
            false,
            false,
            ['LayerA'],
        ];

        yield [
            false,
            false,
            false,
            [],
        ];
    }

    /**
     * @dataProvider provideGetLayersByClassLikeName
     */
    public function testGetLayersByClassLikeName(bool $collectA, bool $collectB1, bool $collectB2, array $expectedLayers): void
    {
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getLayers')->willReturn([
            ConfigurationLayer::fromArray([
                'name' => 'LayerA',
                'collectors' => [
                    ['type' => 'CollectorA'],
                ],
            ]),
            ConfigurationLayer::fromArray([
                'name' => 'LayerB',
                'collectors' => [
                    ['type' => 'CollectorB1'],
                    ['type' => 'CollectorB2'],
                ],
            ]),
        ]);
        $configuration->method('getParameters')->willReturn([]);

        $astMap = $this->createMock(AstMap::class);
        $collectorRegistry = $this->createMock(Registry::class);
        $collectorRegistry->method('getCollector')->willReturnMap([
            ['CollectorA', $this->getCollector($collectA)],
            ['CollectorB1', $this->getCollector($collectB1)],
            ['CollectorB2', $this->getCollector($collectB2)],
        ]);

        $resolver = new TokenLayerResolver(
            $configuration,
            $astMap,
            $collectorRegistry,
            new ParameterResolver()
        );

        self::assertEquals(
            $expectedLayers,
            $resolver->getLayersByTokenName(ClassLikeName::fromFQCN('classA'))
        );
    }

    public function testCircularDependency(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Circular dependency between layers detected');

        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getLayers')
            ->willReturn([
                ConfigurationLayer::fromArray([
                    'name' => 'LayerA',
                    'collectors' => [
                        [
                            'type' => 'layer',
                            'layer' => 'LayerB',
                        ],
                    ],
                ]),
                ConfigurationLayer::fromArray([
                    'name' => 'LayerB',
                    'collectors' => [
                        [
                            'type' => 'layer',
                            'layer' => 'LayerA',
                        ],
                    ],
                ]),
            ]);
        $configuration->method('getParameters')
            ->willReturn([]);

        $collectorRegistry = $this->createMock(Registry::class);
        $collectorRegistry->method('getCollector')
            ->willReturnMap([
                ['layer', new LayerCollector()],
            ]);

        $resolver = new TokenLayerResolver(
            $configuration, $this->createMock(AstMap::class), $collectorRegistry, new ParameterResolver()
        );
        $resolver->getLayersByTokenName(ClassLikeName::fromFQCN('GET'));
    }
}
