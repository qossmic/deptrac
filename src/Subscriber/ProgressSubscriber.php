<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Subscriber;

use SensioLabs\Deptrac\AstRunner\Event\AstFileAnalyzedEvent;
use SensioLabs\Deptrac\AstRunner\Event\PostCreateAstMapEvent;
use SensioLabs\Deptrac\AstRunner\Event\PreCreateAstMapEvent;
use SensioLabs\Deptrac\Console\Output;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProgressSubscriber implements EventSubscriberInterface
{
    /** @var Output */
    private $output;

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
            AstFileAnalyzedEvent::class => 'onAstFileAnalyzedEvent',
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

    public function onAstFileAnalyzedEvent(AstFileAnalyzedEvent $analyzedEvent): void
    {
        $this->output->getStyle()->progressAdvance();
    }
}
