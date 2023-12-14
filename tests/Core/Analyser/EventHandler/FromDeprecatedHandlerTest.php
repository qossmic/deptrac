<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Analyser\EventHandler;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Analyser\AnalysisResult;
use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Core\Analyser\EventHandler\FromDeprecatedHandler;
use Qossmic\Deptrac\Core\Analyser\EventHandler\MatchingLayersHandler;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType;
use Qossmic\Deptrac\Core\Dependency\Dependency;

class FromDeprecatedHandlerTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = MatchingLayersHandler::getSubscribedEvents();

        self::assertCount(1, $subscribedEvents);
        self::assertArrayHasKey(ProcessEvent::class, $subscribedEvents);
        self::assertSame(['invoke', 1], $subscribedEvents[ProcessEvent::class]);
    }

    private function makeEvent(array $dependerTags, array $dependentTags): ProcessEvent
    {
        $dependerToken = ClassLikeToken::fromFQCN('DependerClass');
        $dependentToken = ClassLikeToken::fromFQCN('DependentClass');

        $event = new ProcessEvent(
            new Dependency(
                $dependerToken,
                $dependentToken,
                new FileOccurrence('test', 1),
                DependencyType::STATIC_METHOD
            ),
            new ClassLikeReference($dependerToken, ClassLikeType::TYPE_CLASS,
                [], [], $dependerTags),
            'DependerLayer',
            new ClassLikeReference($dependentToken, ClassLikeType::TYPE_CLASS,
                [], [], $dependentTags),
            ['DependentLayer'],
            new AnalysisResult()
        );

        return $event;
    }

    public function testInvokeEnabled(): void
    {
        $handler = new FromDeprecatedHandler(['skip_deprecated' => true]);

        $event = $this->makeEvent([], []);
        $handler->invoke($event);

        $this->assertFalse(
            $event->isPropagationStopped(),
            'Propagation should continue if neither reference has the "deprecated" tag'
        );

        $event = $this->makeEvent([], ['@deprecated' => '']);
        $handler->invoke($event);

        $this->assertFalse(
            $event->isPropagationStopped(),
            'Propagation should continue if only the dependent is deprecated'
        );

        $event = $this->makeEvent(['@deprecated' => ''], []);
        $handler->invoke($event);

        $this->assertTrue(
            $event->isPropagationStopped(),
            'Propagation should be stopped if the depender is deprecated'
        );
    }

    public function testInvokeDisabled(): void
    {
        $handler = new FromDeprecatedHandler(['skip_deprecated' => false]);

        $event = $this->makeEvent(['@deprecated' => ''], []);
        $handler->invoke($event);

        $this->assertFalse(
            $event->isPropagationStopped(),
            'Propagation should continue if skip_deprecated is false'
        );
    }
}
