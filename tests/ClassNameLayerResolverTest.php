<?php

namespace Tests\SensioLabs\Deptrac;

use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\ClassNameLayerResolver;
use SensioLabs\Deptrac\Collector\CollectorInterface;
use SensioLabs\Deptrac\CollectorFactory;
use SensioLabs\Deptrac\Configuration;
use SensioLabs\Deptrac\Configuration\ConfigurationCollector;
use SensioLabs\Deptrac\Configuration\ConfigurationLayer;
use Prophecy\Argument;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;

class ClassNameLayerResolverTest extends \PHPUnit_Framework_TestCase
{
    private function getCollectorConfiguration($type)
    {
        $collectorConfiguration = $this->prophesize(ConfigurationCollector::class);
        $collectorConfiguration->getType()->willReturn($type);

        return $collectorConfiguration->reveal();
    }

    private function getCollector($return)
    {
        $collector = $this->prophesize(CollectorInterface::class);
        $collector->satisfy(
            Argument::type('array'),
            Argument::type(AstClassReferenceInterface::class),
            Argument::type(AstMap::class),
            Argument::type(CollectorFactory::class),
            Argument::type(AstParserInterface::class)
        )->willReturn($return);

        return $collector->reveal();
    }

    public function provideGetLayersByClassName()
    {
        yield [
            1, 1, 1, ['LayerA', 'LayerB'],
        ];

        yield [
            1, 0, 1, ['LayerA', 'LayerB'],
        ];

        yield [
            0, 0, 1, ['LayerB'],
        ];

        yield [
            1, 1, 0, ['LayerA', 'LayerB'],
        ];

        yield [
            1, 0, 0, ['LayerA'],
        ];

        yield [
            0, 0, 0, [],
        ];
    }

    /**
     * @param $collectA
     * @param $collectB1
     * @param $collectB2
     * @param array $expectedLayers
     * @dataProvider provideGetLayersByClassName
     */
    public function testGetLayersByClassName($collectA, $collectB1, $collectB2, array $expectedLayers)
    {
        $configuration = $this->prophesize(Configuration::class);
        $configuration->getLayers()->willReturn([
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

        $astMap = $this->prophesize(AstMap::class);
        $collectorFactory = $this->prophesize(CollectorFactory::class);
        $collectorFactory->getCollector('CollectorA')->willReturn(
            $this->getCollector($collectA, ['type' => 'CollectorA', 'foo' => 'bar'])
        );
        $collectorFactory->getCollector('CollectorB1')->willReturn(
            $this->getCollector($collectB1, ['type' => 'CollectorB', 'foo' => 'bar'])
        );
        $collectorFactory->getCollector('CollectorB2')->willReturn(
            $this->getCollector($collectB2, ['type' => 'CollectorB', 'foo' => 'bar'])
        );

        $resolver = new ClassNameLayerResolver(
            $configuration->reveal(),
            $astMap->reveal(),
            $collectorFactory->reveal(),
            $this->prophesize(AstParserInterface::class)->reveal()
        );

        $this->assertEquals(
            $expectedLayers,
            $resolver->getLayersByClassName('classA')
        );
    }
}
