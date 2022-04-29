<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Dependency;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Dependency\DependencyResolver;
use Qossmic\Deptrac\Dependency\Emitter\ClassDependencyEmitter;
use Qossmic\Deptrac\Dependency\Emitter\ClassSuperglobalDependencyEmitter;
use Qossmic\Deptrac\Dependency\Emitter\EmitterTypes;
use Qossmic\Deptrac\Dependency\Emitter\FileDependencyEmitter;
use Qossmic\Deptrac\Dependency\Emitter\FunctionDependencyEmitter;
use Qossmic\Deptrac\Dependency\Emitter\FunctionSuperglobalDependencyEmitter;
use Qossmic\Deptrac\Dependency\Emitter\UsesDependencyEmitter;
use Qossmic\Deptrac\Dependency\Event\PostEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PostFlattenEvent;
use Qossmic\Deptrac\Dependency\Event\PreEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PreFlattenEvent;
use Qossmic\Deptrac\Dependency\InheritanceFlattener;
use Qossmic\Deptrac\Exception\ShouldNotHappenException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DependencyResolverTest extends TestCase
{
    private EventDispatcherInterface $dispatcher;
    private InheritanceFlattener $flattener;
    private ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->flattener = $this->createMock(InheritanceFlattener::class);

        $this->container = new ContainerBuilder();
        $this->container->set(EmitterTypes::CLASS_TOKEN, new ClassDependencyEmitter());
        $this->container->set(EmitterTypes::CLASS_SUPERGLOBAL_TOKEN, new ClassSuperglobalDependencyEmitter());
        $this->container->set(EmitterTypes::FILE_TOKEN, new FileDependencyEmitter());
        $this->container->set(EmitterTypes::FUNCTION_TOKEN, new FunctionDependencyEmitter());
        $this->container->set(EmitterTypes::FUNCTION_SUPERGLOBAL_TOKEN, new FunctionSuperglobalDependencyEmitter());
        $this->container->set(EmitterTypes::USE_TOKEN, new UsesDependencyEmitter());
    }

    public function testResolveWithDefaultEmitters(): void
    {
        $astMap = new AstMap([]);

        $this->dispatcher->method('dispatch')->withConsecutive(
            [new PreEmitEvent('ClassDependencyEmitter')],
            [new PostEmitEvent()],
            [new PreEmitEvent('UsesDependencyEmitter')],
            [new PostEmitEvent()],
            [new PreFlattenEvent()],
            [new PostFlattenEvent()]
        );
        $this->flattener->expects(self::once())->method('flattenDependencies');

        $resolver = new DependencyResolver(
            [],
            $this->flattener,
            $this->container,
            $this->dispatcher
        );

        $resolver->resolve($astMap);
    }

    public function testResolveWithCustomEmitters(): void
    {
        $astMap = new AstMap([]);

        $this->dispatcher->method('dispatch')->withConsecutive(
            [new PreEmitEvent('FunctionDependencyEmitter')],
            [new PostEmitEvent()],
            [new PreFlattenEvent()],
            [new PostFlattenEvent()]
        );
        $this->flattener->expects(self::once())->method('flattenDependencies');

        $resolver = new DependencyResolver(
            ['types' => [EmitterTypes::FUNCTION_TOKEN]],
            $this->flattener,
            $this->container,
            $this->dispatcher
        );

        $resolver->resolve($astMap);
    }

    public function testResolveWithInvalidEmitterType(): void
    {
        $astMap = new AstMap([]);

        $this->dispatcher->expects(self::never())->method('dispatch');
        $this->flattener->expects(self::never())->method('flattenDependencies');

        $resolver = new DependencyResolver(
            ['types' => ['invalid']],
            $this->flattener,
            $this->container,
            $this->dispatcher
        );

        $this->expectException(ShouldNotHappenException::class);

        $resolver->resolve($astMap);
    }
}
