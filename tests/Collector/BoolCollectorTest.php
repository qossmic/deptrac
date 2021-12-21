<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Collector;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;
use Qossmic\Deptrac\Collector\BoolCollector;
use Qossmic\Deptrac\Collector\ClassNameCollector;
use Qossmic\Deptrac\Collector\CollectorInterface;
use Qossmic\Deptrac\Collector\Registry;

final class BoolCollectorTest extends TestCase
{
    public function testSatisfy(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"bool" collector must have a "must" or a "must_not" attribute.');

        (new BoolCollector())->satisfy(
            [],
            $this->createMock(AstClassReference::class),
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );
    }

    public function testResolvable(): void
    {
        $configuration = [
            'must' => [
                [
                    'type' => (new ClassNameCollector())->getType(),
                    'regex' => '',
                ],
            ],
        ];
        $collectorRegistry = $this->createMock(Registry::class);
        $collectorRegistry->method('getCollector')
            ->willReturnMap([
                [(new ClassNameCollector())->getType(), new ClassNameCollector()],
            ]);

        $stat = (new BoolCollector())->resolvable($configuration, $collectorRegistry, []);

        self::assertEquals(true, $stat);
    }

    public function testUnresolvable(): void
    {
        $type = 'true';
        $configuration = [
            'must' => [
                [
                    'type' => $type,
                    'regex' => '',
                ],
            ],
        ];
        $collectorRegistry = $this->createMock(Registry::class);
        $collectorRegistry->method('getCollector')
            ->willReturnMap([
                [$type, $this->getCollectorMock((bool) $type, false)],
            ]);

        $stat = (new BoolCollector())->resolvable($configuration, $collectorRegistry, []);

        self::assertEquals(false, $stat);
    }

    public function testType(): void
    {
        self::assertEquals('bool', (new BoolCollector())->getType());
    }

    public function provideSatisfyBasic(): iterable
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
    public function testSatisfyBasicTest(array $configuration, bool $expected): void
    {
        $collectorFactory = $this->createMock(Registry::class);
        $collectorFactory->method('getCollector')->willReturnMap([
            ['true', $this->getCollectorMock(true, true)],
            ['false', $this->getCollectorMock(false, true)],
        ]);

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
            $this->createMock(AstClassReference::class),
            $this->createMock(AstMap::class),
            $collectorFactory
        );

        self::assertEquals($expected, $stat);
    }

    private function getCollectorMock(bool $returns, bool $resolvable)
    {
        $collector = $this->createMock(CollectorInterface::class);
        $collector
            ->method('satisfy')
            ->with(
                ['type' => $returns ? 'true' : 'false', 'foo' => 'bar'],
                self::isInstanceOf(AstClassReference::class),
                self::isInstanceOf(AstMap::class),
                self::isInstanceOf(Registry::class)
            )
            ->willReturn($returns);
        $collector
            ->method('resolvable')
            ->willReturn($resolvable);

        return $collector;
    }
}
