<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Subscriber;

use Qossmic\Deptrac\Ast\Event\PostCreateAstMapEvent;
use Qossmic\Deptrac\Ast\Event\PreCreateAstMapEvent;
use Qossmic\Deptrac\Ast\Parser\Cache\AstFileReferenceDeferredCacheInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CacheableFileSubscriber implements EventSubscriberInterface
{
    private AstFileReferenceDeferredCacheInterface $deferredCache;

    public function __construct(AstFileReferenceDeferredCacheInterface $deferredCache)
    {
        $this->deferredCache = $deferredCache;
    }

    /**
     * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PreCreateAstMapEvent::class => 'onPreCreateAstMapEvent',
            PostCreateAstMapEvent::class => 'onPostCreateAstMapEvent',
        ];
    }

    public function onPreCreateAstMapEvent(PreCreateAstMapEvent $event): void
    {
        $this->deferredCache->load();
    }

    public function onPostCreateAstMapEvent(PostCreateAstMapEvent $event): void
    {
        $this->deferredCache->write();
    }
}
