<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Supportive\Console\Subscriber;

use Qossmic\Deptrac\Contract\Ast\AstFileAnalysedEvent;
use Qossmic\Deptrac\Contract\Ast\PostCreateAstMapEvent;
use Qossmic\Deptrac\Contract\Ast\PreCreateAstMapEvent;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use DEPTRAC_202401\Symfony\Component\EventDispatcher\EventSubscriberInterface;
class ProgressSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly OutputInterface $output)
    {
    }
    /**
     * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents() : array
    {
        return [PreCreateAstMapEvent::class => 'onPreCreateAstMapEvent', PostCreateAstMapEvent::class => ['onPostCreateAstMapEvent', 1], AstFileAnalysedEvent::class => 'onAstFileAnalysedEvent'];
    }
    public function onPreCreateAstMapEvent(PreCreateAstMapEvent $preCreateAstMapEvent) : void
    {
        $this->output->getStyle()->progressStart($preCreateAstMapEvent->expectedFileCount);
    }
    public function onPostCreateAstMapEvent(PostCreateAstMapEvent $postCreateAstMapEvent) : void
    {
        $this->output->getStyle()->progressFinish();
    }
    public function onAstFileAnalysedEvent(AstFileAnalysedEvent $analysedEvent) : void
    {
        $this->output->getStyle()->progressAdvance();
    }
}
