<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Layer\Collector;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Layer\InvalidCollectorDefinitionException;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Layer\Collector\BoolCollector;
use Qossmic\Deptrac\Core\Layer\Collector\Collectable;
use Qossmic\Deptrac\Core\Layer\Collector\CollectorResolverInterface;
use Qossmic\Deptrac\Core\Layer\Collector\ConditionalCollectorInterface;

final class BoolCollectorTest extends TestCase
{
    private BoolCollector $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $collector = $this->createMock(ConditionalCollectorInterface::class);
        $collector
            ->method('resolvable')
            ->with($this->callback(static fn (array $config): bool => 'custom' === $config['type']))
            ->willReturnCallback(static fn (array $config): bool => (bool) $config['resolvable'] ?? false);
        $collector
            ->method('satisfy')
            ->with($this->callback(static fn (array $config): bool => 'custom' === $config['type']))
            ->willReturnCallback(static fn (array $config): bool => (bool) $config['satisfy'] ?? false);

        $resolver = $this->createMock(CollectorResolverInterface::class);
        $resolver
            ->method('resolve')
            ->with($this->callback(static fn (array $config): bool => 'custom' === $config['type']))
            ->willReturnCallback(static fn (array $config): Collectable => new Collectable($collector, $config));

        $this->collector = new BoolCollector($resolver);
    }

    public static function provideResolvableConfiguration(): iterable
    {
        $true = [
            'type' => 'custom',
            'resolvable' => true,
        ];

        $false = [
            'type' => 'custom',
            'resolvable' => false,
        ];

        yield 'must with resolvable collector' => [
            [
                'must' => [$true],
            ],
            true,
        ];

        yield 'must with unresolvable collector' => [
            [
                'must' => [$false],
            ],
            false,
        ];

        yield 'must_any with resolvable collector' => [
            [
                'must_any' => [$true],
            ],
            true,
        ];

        yield 'must_any with unresolvable collector' => [
            [
                'must_any' => [$false],
            ],
            false,
        ];

        yield 'must_not with resolvable collector' => [
            [
                'must_not' => [$true],
            ],
            true,
        ];

        yield 'must_not with unresolvable collector' => [
            [
                'must_not' => [$false],
            ],
            false,
        ];

        yield 'must with multiple collectors, unresolvable' => [
            [
                'must' => [$true, $false, $true],
            ],
            false,
        ];

        yield 'must_any with multiple collectors, unresolvable' => [
            [
                'must' => [$true, $false, $true],
            ],
            false,
        ];

        yield 'must_not with multiple collectors, unresolvable' => [
            [
                'must_not' => [$true, $true, $false],
            ],
            false,
        ];

        yield 'mixed with must_not unresolvable' => [
            [
                'must' => [$true],
                'must_any' => [$true],
                'must_not' => [$false],
            ],
            false,
        ];

        yield 'mixed with must unresolvable' => [
            [
                'must' => [$false],
                'must_any' => [$true],
                'must_not' => [$true],
            ],
            false,
        ];

        yield 'mixed with must_any unresolvable' => [
            [
                'must' => [$true],
                'must_any' => [$false],
                'must_not' => [$true],
            ],
            false,
        ];

        yield 'mixed with all resolvable' => [
            [
                'must' => [$true],
                'must_any' => [$true],
                'must_not' => [$true],
            ],
            true,
        ];
    }

    /**
     * @dataProvider provideResolvableConfiguration
     */
    public function testResolvable(array $config, bool $expectedOutcome): void
    {
        $actualOutcome = $this->collector->resolvable($config);

        self::assertSame($expectedOutcome, $actualOutcome);
    }

    public static function providesatisfiableConfiguration(): iterable
    {
        $true = [
            'type' => 'custom',
            'satisfy' => true,
        ];
        $false = [
            'type' => 'custom',
            'satisfy' => false,
        ];

        yield 'must with satisfiable collector' => [
            [
                'must' => [$true],
            ],
            true,
        ];

        yield 'must with unsatisfiable collector' => [
            [
                'must' => [$false],
            ],
            false,
        ];

        yield 'must_any with satisfiable collector' => [
            [
                'must_any' => [$true],
            ],
            true,
        ];

        yield 'must_any with unsatisfiable collector' => [
            [
                'must_any' => [$false],
            ],
            false,
        ];

        yield 'must_not with satisfiable collector' => [
            [
                'must_not' => [$true],
            ],
            false,
        ];

        yield 'must_not with unsatisfiable collector' => [
            [
                'must_not' => [$false],
            ],
            true,
        ];

        yield 'must with multiple collectors, unsatisfiable' => [
            [
                'must' => [$false, $true, $true],
            ],
            false,
        ];

        yield 'must_any with multiple collectors, one satisfiable' => [
            [
                'must_any' => [$false, $true, $false],
            ],
            true,
        ];

        yield 'must_not with multiple collectors, unsatisfiable' => [
            [
                'must_not' => [$false, $true, $false],
            ],
            false,
        ];

        yield 'mixed with must_not unsatisfiable' => [
            [
                'must' => [$true],
                'must_any' => [$true],
                'must_not' => [$true],
            ],
            false,
        ];

        yield 'mixed with must unsatisfiable' => [
            [
                'must' => [$false],
                'must_any' => [$true],
                'must_not' => [$false],
            ],
            false,
        ];

        yield 'mixed with must_any unsatisfiable' => [
            [
                'must' => [$true],
                'must_any' => [$false],
                'must_not' => [$false],
            ],
            false,
        ];

        yield 'mixed with all satisfiable' => [
            [
                'must' => [$true],
                'must_any' => [$true, $false],
                'must_not' => [$false, $false],
            ],
            true,
        ];
    }

    /**
     * @dataProvider provideSatisfiableConfiguration
     */
    public function testSatisfy(array $config, bool $expectedOutcome): void
    {
        $reference = new ClassLikeReference(ClassLikeToken::fromFQCN('App\\Foo'));
        $actualOutcome = $this->collector->satisfy($config, $reference);

        self::assertSame($expectedOutcome, $actualOutcome);
    }

    public function testThrowsOnInvalidConfiguration(): void
    {
        $config = [
            [
                'must' => [
                    [
                        'type' => 'custom',
                        'satisfy' => true,
                    ],
                ],
                'must_any' => [
                    [
                        'type' => 'custom',
                        'satisfy' => false,
                    ],
                ],
                'must_not' => [
                    [
                        'type' => 'custom',
                        'satisfy' => false,
                    ],
                ],
                'invalid' => [
                    [
                        'type' => 'custom',
                        'satisfy' => false,
                    ],
                ],
            ],
            true,
        ];
        $reference = new ClassLikeReference(ClassLikeToken::fromFQCN('App\\Foo'));

        $this->expectException(InvalidCollectorDefinitionException::class);
        $this->expectExceptionMessage('"bool" collector must have at least one of "must", "must_any", or "must_not" attribute.');

        $this->collector->satisfy($config, $reference);
    }

    public function testThrowsOnEmptyConfiguration(): void
    {
        $config = [
            [],
            true,
        ];
        $reference = new ClassLikeReference(ClassLikeToken::fromFQCN('App\\Foo'));

        $this->expectException(InvalidCollectorDefinitionException::class);
        $this->expectExceptionMessage('"bool" collector must have at least one of "must", "must_any", or "must_not" attribute.');

        $this->collector->satisfy($config, $reference);
    }
}
