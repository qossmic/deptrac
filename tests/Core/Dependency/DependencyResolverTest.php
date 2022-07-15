<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Dependency;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Qossmic\Deptrac\Contract\Dependency\PostEmitEvent;
use Qossmic\Deptrac\Contract\Dependency\PostFlattenEvent;
use Qossmic\Deptrac\Contract\Dependency\PreEmitEvent;
use Qossmic\Deptrac\Contract\Dependency\PreFlattenEvent;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Dependency\DependencyResolver;
use Qossmic\Deptrac\Core\Dependency\Emitter\ClassDependencyEmitter;
use Qossmic\Deptrac\Core\Dependency\Emitter\ClassSuperglobalDependencyEmitter;
use Qossmic\Deptrac\Core\Dependency\Emitter\FileDependencyEmitter;
use Qossmic\Deptrac\Core\Dependency\Emitter\FunctionDependencyEmitter;
use Qossmic\Deptrac\Core\Dependency\Emitter\FunctionSuperglobalDependencyEmitter;
use Qossmic\Deptrac\Core\Dependency\Emitter\UsesDependencyEmitter;
use Qossmic\Deptrac\Core\Dependency\InheritanceFlattener;
use Qossmic\Deptrac\Supportive\DependencyInjection\Configuration;
use Qossmic\Deptrac\Supportive\DependencyInjection\EmitterTypes;
use Qossmic\Deptrac\Supportive\ShouldNotHappenException;
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
            ['types' => Configuration::DEFAULT_EMITTER_TYPES],
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
