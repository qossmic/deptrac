<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Subscriber;

use Qossmic\Deptrac\AstRunner\Event\AstFileAnalysedEvent;
use Qossmic\Deptrac\AstRunner\Event\PostCreateAstMapEvent;
use Qossmic\Deptrac\AstRunner\Event\PreCreateAstMapEvent;
use Qossmic\Deptrac\Console\Output;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProgressSubscriber implements EventSubscriberInterface
{
    private Output $output;

    public function __construct(Output $output)
    {
        $this->output = $output;
    }

    /**
     * @return array<string, string|array>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PreCreateAstMapEvent::class => 'onPreCreateAstMapEvent',
            PostCreateAstMapEvent::class => ['onPostCreateAstMapEvent', 1],
            AstFileAnalysedEvent::class => 'onAstFileAnalysedEvent',
        ];
    }

    public function onPreCreateAstMapEvent(PreCreateAstMapEvent $preCreateAstMapEvent): void
    {
        $this->output->getStyle()->progressStart($preCreateAstMapEvent->getExpectedFileCount());
    }

    public function onPostCreateAstMapEvent(PostCreateAstMapEvent $postCreateAstMapEvent): void
    {
        $this->output->getStyle()->progressFinish();
    }

    public function onAstFileAnalysedEvent(AstFileAnalysedEvent $analysedEvent): void
    {
        $this->output->getStyle()->progressAdvance();
    }
}
