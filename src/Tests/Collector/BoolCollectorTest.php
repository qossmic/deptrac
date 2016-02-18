<?php

namespace DependencyTracker\Tests\Collector;

use DependencyTracker\Collector\BoolCollector;
use DependencyTracker\Collector\CollectorInterface;
use DependencyTracker\CollectorFactory;
use Prophecy\Argument;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;

class BoolCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStatisfy()
    {
        $stat = (new BoolCollector())->satisfy(
            [],
            $this->prophesize(AstClassReferenceInterface::class)->reveal(),
            $this->prophesize(AstMap::class)->reveal(),
            $this->prophesize(CollectorFactory::class)->reveal()
        );

        $this->assertEquals(true, $stat);
    }

    public function testType()
    {
        $this->assertEquals('bool', (new BoolCollector())->getType());
    }

    public function getCalculatorMock($returns)
    {
        $collector = $this->prophesize(CollectorInterface::class);
        $collector->satisfy(
            ['type' => $returns, 'foo' => 'bar'],
            Argument::type(AstClassReferenceInterface::class),
            Argument::type(AstMap::class),
            Argument::type(CollectorFactory::class)
        )->willReturn($returns);

        return $collector->reveal();
    }

    public function provideStatisfyBasic()
    {
        # must
        yield [
            [
                'must' => [
                    ['type' => true],
                ],
            ],
            true,
        ];

        yield [
            [
                'must' => [
                    ['type' => true],
                    ['type' => true],
                ],
            ],
            true,
        ];

        yield [
            [
                'must' => [
                    ['type' => true],
                    ['type' => false],
                ],
            ],
            false,
        ];

        yield [
            [
                'must' => [
                    ['type' => false],
                    ['type' => true],
                ],
            ],
            false,
        ];

        yield [
            [
                'must' => [
                    ['type' => false],
                ],
            ],
            false,
        ];

        # must not
        yield [
            [
                'must_not' => [
                    ['type' => false],
                ],
            ],
            true,
        ];

        yield [
            [
                'must_not' => [
                    ['type' => true],
                ],
            ],
            false,
        ];

        yield [
            [
                'must_not' => [
                    ['type' => true],
                    ['type' => false],
                ],
            ],
            false,
        ];

        yield [
            [
                'must_not' => [
                    ['type' => false],
                    ['type' => false],
                ],
            ],
            true,
        ];

        yield [
            [
                'must_not' => [
                    ['type' => true],
                    ['type' => true],
                ],
            ],
            false,
        ];
    }

    /**
     * @dataProvider provideStatisfyBasic
     */
    public function testStatisfyBasicTest($configuration, $expected)
    {
        $collectorFactory = $this->prophesize(CollectorFactory::class);
        $collectorFactory->getCollector(true)->willReturn(
            $this->getCalculatorMock(true)
        );
        $collectorFactory->getCollector(false)->willReturn(
            $this->getCalculatorMock(false)
        );

        if (isset($configuration['must'])) {
            foreach ($configuration['must'] as &$v) {
                $v['foo'] = 'bar';
            }
        }
        if (isset($configuration['must_not'])) {
            foreach ($configuration['must_not'] as &$v) {
                $v['foo'] = 'bar';
            }
        }

        $stat = (new BoolCollector())->satisfy(
            $configuration,
            $this->prophesize(AstClassReferenceInterface::class)->reveal(),
            $this->prophesize(AstMap::class)->reveal(),
            $collectorFactory->reveal()
        );

        $this->assertEquals($expected, $stat);
    }
}
