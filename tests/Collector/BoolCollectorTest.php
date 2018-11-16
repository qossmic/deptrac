<?php

namespace Tests\SensioLabs\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\Collector\BoolCollector;
use SensioLabs\Deptrac\Collector\Registry;
use SensioLabs\Deptrac\Collector\CollectorInterface;

class BoolCollectorTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSatisfy()
    {
        $stat = (new BoolCollector())->satisfy(
            [],
            $this->prophesize(AstClassReferenceInterface::class)->reveal(),
            $this->prophesize(AstMap::class)->reveal(),
            $this->prophesize(Registry::class)->reveal(),
            $this->prophesize(AstParserInterface::class)->reveal()
        );

        $this->assertEquals(true, $stat);
    }

    public function testType()
    {
        $this->assertEquals('bool', (new BoolCollector())->getType());
    }

    public function getCalculatorMock(bool $returns)
    {
        $collector = $this->prophesize(CollectorInterface::class);
        $collector->satisfy(
            ['type' => $returns ? 'true' : 'false', 'foo' => 'bar'],
            Argument::type(AstClassReferenceInterface::class),
            Argument::type(AstMap::class),
            Argument::type(Registry::class),
            Argument::type(AstParserInterface::class)
        )->willReturn($returns);

        return $collector->reveal();
    }

    public function provideSatisfyBasic()
    {
        // must
        yield [
            [
                'must' => [
                    ['type' => 'true'],
                ],
            ],
            true,
        ];

        yield [
            [
                'must' => [
                    ['type' => 'true'],
                    ['type' => 'true'],
                ],
            ],
            true,
        ];

        yield [
            [
                'must' => [
                    ['type' => 'true'],
                    ['type' => 'false'],
                ],
            ],
            false,
        ];

        yield [
            [
                'must' => [
                    ['type' => 'false'],
                    ['type' => 'true'],
                ],
            ],
            false,
        ];

        yield [
            [
                'must' => [
                    ['type' => 'false'],
                ],
            ],
            false,
        ];

        // must not
        yield [
            [
                'must_not' => [
                    ['type' => 'false'],
                ],
            ],
            true,
        ];

        yield [
            [
                'must_not' => [
                    ['type' => 'true'],
                ],
            ],
            false,
        ];

        yield [
            [
                'must_not' => [
                    ['type' => 'true'],
                    ['type' => 'false'],
                ],
            ],
            false,
        ];

        yield [
            [
                'must_not' => [
                    ['type' => 'false'],
                    ['type' => 'false'],
                ],
            ],
            true,
        ];

        yield [
            [
                'must_not' => [
                    ['type' => 'true'],
                    ['type' => 'true'],
                ],
            ],
            false,
        ];
    }

    /**
     * @dataProvider provideSatisfyBasic
     */
    public function testSatisfyBasicTest(array $configuration, bool $expected)
    {
        $collectorFactory = $this->prophesize(Registry::class);
        $collectorFactory->getCollector('true')->willReturn(
            $this->getCalculatorMock(true)
        );
        $collectorFactory->getCollector('false')->willReturn(
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
            $collectorFactory->reveal(),
            $this->prophesize(AstParserInterface::class)->reveal()
        );

        $this->assertEquals($expected, $stat);
    }
}
