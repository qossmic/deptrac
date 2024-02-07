<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Analyser\EventHandler;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Analyser\AnalysisResult;
use Qossmic\Deptrac\Contract\Analyser\EventHelper;
use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use Qossmic\Deptrac\Contract\Ast\DependencyContext;
use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Contract\Layer\LayerProvider;
use Qossmic\Deptrac\Core\Analyser\EventHandler\DependsOnInternalToken;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType;
use Qossmic\Deptrac\Core\Dependency\Dependency;

final class DependsOnInternalTokenTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = DependsOnInternalToken::getSubscribedEvents();

        self::assertCount(1, $subscribedEvents);
        self::assertArrayHasKey(ProcessEvent::class, $subscribedEvents);
        self::assertSame(['invoke', -2], $subscribedEvents[ProcessEvent::class]);
    }

    private function makeEvent(
        array $dependerTags, array $dependentTags, $dependentLayer = 'DependentLayer'
    ): ProcessEvent {
        $dependerToken = ClassLikeToken::fromFQCN('DependerClass');
        $dependentToken = ClassLikeToken::fromFQCN('DependentClass');

        $event = new ProcessEvent(
            new Dependency(
                $dependerToken,
                $dependentToken,
                new DependencyContext(new FileOccurrence('test', 1),
                    DependencyType::STATIC_METHOD)
            ),
            new ClassLikeReference($dependerToken, ClassLikeType::TYPE_CLASS,
                [], [], $dependerTags),
            'DependerLayer',
            new ClassLikeReference($dependentToken, ClassLikeType::TYPE_CLASS,
                [], [], $dependentTags),
            [$dependentLayer => true],
            new AnalysisResult()
        );

        return $event;
    }

    public function testInvoke(): void
    {
        $helper = new EventHelper([], new LayerProvider([]));
        $handler = new DependsOnInternalToken($helper, ['internal_tag' => '@layer-internal']);

        $event = $this->makeEvent([], []);
        $handler->invoke($event);

        $this->assertFalse(
            $event->isPropagationStopped(),
            'Propagation should continue if neither reference has the "layer-internal" tag'
        );

        $event = $this->makeEvent(['@layer-internal' => ['']], []);
        $handler->invoke($event);

        $this->assertFalse(
            $event->isPropagationStopped(),
            'Propagation should continue if only the depender is marked @layer-internal'
        );

        $event = $this->makeEvent([], ['@layer-internal' => ['']]);
        $handler->invoke($event);

        $this->assertTrue(
            $event->isPropagationStopped(),
            'Propagation should be stopped if the dependent is marked @layer-internal'
        );

        $event = $this->makeEvent([], ['@layer-internal' => ['']], 'DependerLayer');
        $handler->invoke($event);

        $this->assertFalse(
            $event->isPropagationStopped(),
            'Propagation should not be stopped if the dependent is marked @layer-internal '.
            'but dependent is in the same layer'
        );

        $event = $this->makeEvent([], ['@deptrac-internal' => ['']]);
        $handler->invoke($event);

        $this->assertTrue(
            $event->isPropagationStopped(),
            'Propagation should be stopped if the dependent is marked @deptrac-internal'
        );
    }

    public function testDefaultInternalTag(): void
    {
        $helper = new EventHelper([], new LayerProvider([]));
        $handler = new DependsOnInternalToken($helper, ['internal_tag' => null]);

        $event = $this->makeEvent([], ['@internal' => ['']]);
        $handler->invoke($event);

        $this->assertFalse(
            $event->isPropagationStopped(),
            'The @internal tag should not be used per default'
        );

        $event = $this->makeEvent([], ['@deptrac-internal' => ['']]);
        $handler->invoke($event);

        $this->assertTrue(
            $event->isPropagationStopped(),
            'The @deptrac-internal tag should be used per default'
        );
    }
}
