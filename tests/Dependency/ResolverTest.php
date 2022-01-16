<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\Dependency\Emitter\ClassDependencyEmitter;
use Qossmic\Deptrac\Dependency\Emitter\UseDependencyEmitter;
use Qossmic\Deptrac\Dependency\Event\PostEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PostFlattenEvent;
use Qossmic\Deptrac\Dependency\Event\PreEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PreFlattenEvent;
use Qossmic\Deptrac\Dependency\InheritanceFlatter;
use Qossmic\Deptrac\Dependency\Resolver;
use Qossmic\Deptrac\Exception\Dependency\EmitterResolverException;
use Qossmic\Deptrac\Runtime\Analysis\AnalysisContext;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ResolverTest extends TestCase
{
    public function testResolve(): void
    {
        $astMap = new AstMap([]);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->method('dispatch')->withConsecutive(
            [new PreEmitEvent(ClassDependencyEmitter::class)],
            [new PostEmitEvent()],
            [new PreEmitEvent(UseDependencyEmitter::class)],
            [new PostEmitEvent()],
            [new PreFlattenEvent()],
            [new PostFlattenEvent()]
        );

        $inheritanceFlatter = $this->createMock(InheritanceFlatter::class);
        $inheritanceFlatter->expects(self::once())->method('flattenDependencies');
        $analysisContext = new AnalysisContext([AnalysisContext::CLASS_TOKEN, AnalysisContext::USE_TOKEN]);
        $classEmitter = new ClassDependencyEmitter();
        $useEmitter = new UseDependencyEmitter();
        $emitterLocator = new Container();
        $emitterLocator->set(AnalysisContext::CLASS_TOKEN, $classEmitter);
        $emitterLocator->set(AnalysisContext::USE_TOKEN, $useEmitter);

        $resolver = new Resolver($dispatcher, $inheritanceFlatter, $analysisContext, $emitterLocator);

        $resolver->resolve($astMap);
    }

    public function testInvalidConfigurationThrowsException(): void
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $inheritanceFlatter = $this->createMock(InheritanceFlatter::class);
        $analysisContext = new AnalysisContext([AnalysisContext::CLASS_TOKEN]);
        $emptyLocator = new Container();
        $astMap = new AstMap([]);

        $resolver = new Resolver($dispatcher, $inheritanceFlatter, $analysisContext, $emptyLocator);

        $this->expectException(EmitterResolverException::class);
        $this->expectExceptionMessage('No emitter is registered for the provided type "class".');

        $resolver->resolve($astMap);
    }
}
