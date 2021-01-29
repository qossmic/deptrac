<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\Dependency\Event\PostEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PostFlattenEvent;
use Qossmic\Deptrac\Dependency\Event\PreEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PreFlattenEvent;
use Qossmic\Deptrac\Dependency\InheritanceFlatter;
use Qossmic\Deptrac\Dependency\Resolver;
use Qossmic\Deptrac\DependencyEmitter\DependencyEmitterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ResolverTest extends TestCase
{
    public function testResolve(): void
    {
        $astMap = new AstMap([]);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->method('dispatch')->withConsecutive(
            [new PreEmitEvent('emitter')],
            [new PostEmitEvent()],
            [new PreFlattenEvent()],
            [new PostFlattenEvent()]
        );

        $inheritanceFlatter = $this->createMock(InheritanceFlatter::class);
        $inheritanceFlatter->expects(self::once())->method('flattenDependencies');

        $emitter = $this->createMock(DependencyEmitterInterface::class);
        $emitter->method('getName')->willReturn('emitter');
        $emitter->expects(self::once())->method('applyDependencies');

        $resolver = new Resolver($dispatcher, $inheritanceFlatter, [$emitter]);
        $resolver->resolve($astMap);
    }
}
