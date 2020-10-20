<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\Dependency\Event\PostEmitEvent;
use SensioLabs\Deptrac\Dependency\Event\PostFlattenEvent;
use SensioLabs\Deptrac\Dependency\Event\PreEmitEvent;
use SensioLabs\Deptrac\Dependency\Event\PreFlattenEvent;
use SensioLabs\Deptrac\Dependency\InheritanceFlatter;
use SensioLabs\Deptrac\Dependency\Resolver;
use SensioLabs\Deptrac\DependencyEmitter\DependencyEmitterInterface;
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
