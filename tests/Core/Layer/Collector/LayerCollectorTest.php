<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Layer\Collector;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Layer\Collector\LayerCollector;
use Qossmic\Deptrac\Core\Layer\Exception\CircularReferenceException;
use Qossmic\Deptrac\Core\Layer\LayerResolverInterface;

final class LayerCollectorTest extends TestCase
{
    private LayerResolverInterface $resolver;
    private LayerCollector $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = $this->createMock(LayerResolverInterface::class);

        $this->collector = new LayerCollector($this->resolver);
    }

    public function testConfig(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('LayerCollector needs the layer configuration');

        $this->collector->satisfy(
            [],
            new ClassLikeReference(ClassLikeToken::fromFQCN('App\\Foo')),
            new AstMap([])
        );
    }

    public function testResolvable(): void
    {
        $this->resolver
            ->expects($this->once())
            ->method('has')
            ->with('test')
            ->willReturn(true);

        $actual = $this->collector->resolvable(['value' => 'test']);

        self::assertSame(true, $actual);
    }

    public function testUnrsolvable(): void
    {
        $this->resolver
            ->expects($this->once())
            ->method('has')
            ->with('test')
            ->willReturn(false);

        $actual = $this->collector->resolvable(['value' => 'test']);

        self::assertSame(false, $actual);
    }

    public function testSatisfyWithUnknownLayer(): void
    {
        $this->resolver
            ->expects($this->once())
            ->method('has')
            ->with('test')
            ->willReturn(false);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown layer "test" specified in collector.');

        $this->collector->satisfy(
            ['value' => 'test'],
            new ClassLikeReference(ClassLikeToken::fromFQCN('App\\Foo')),
            new AstMap([])
        );
    }

    public function testCircularReference(): void
    {
        $reference = new ClassLikeReference(ClassLikeToken::fromFQCN('App\\Foo'));
        $this->resolver
            ->method('has')
            ->with('FooLayer')
            ->willReturn(true);
        $this->resolver
            ->method('isReferenceInLayer')
            ->with('FooLayer', $reference)
            ->willReturnCallback(function (string $layerName, ClassLikeReference $reference) {
                return $this->collector->satisfy(['value' => 'FooLayer'], $reference);
            });

        $this->expectException(CircularReferenceException::class);
        $this->expectExceptionMessage('Circular dependency between layers detected. Token "App\Foo" could not be resolved.');

        $this->collector->satisfy(
            ['value' => 'FooLayer'],
            $reference,
        );
    }

    public function testSatisfyWhenReferenceIsInLayer(): void
    {
        $reference = new ClassLikeReference(ClassLikeToken::fromFQCN('App\\Foo'));
        $this->resolver
            ->method('has')
            ->with('AppLayer')
            ->willReturn(true);
        $this->resolver
            ->method('isReferenceInLayer')
            ->with('AppLayer', $reference)
            ->willReturn(true);

        $actual = $this->collector->satisfy(
            ['value' => 'AppLayer'],
            $reference,
        );

        self::assertTrue($actual);
    }

    public function testSatisfyWhenReferenceIsNotInLayer(): void
    {
        $reference = new ClassLikeReference(ClassLikeToken::fromFQCN('App\\Foo'));
        $this->resolver
            ->method('has')
            ->with('AppLayer')
            ->willReturn(true);
        $this->resolver
            ->method('isReferenceInLayer')
            ->with('AppLayer', $reference)
            ->willReturn(false);

        $actual = $this->collector->satisfy(
            ['value' => 'AppLayer'],
            $reference,
        );

        self::assertFalse($actual);
    }
}
