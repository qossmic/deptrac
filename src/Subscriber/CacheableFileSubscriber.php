<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Subscriber;

use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceFileCache;
use SensioLabs\Deptrac\AstRunner\Event\PostCreateAstMapEvent;
use SensioLabs\Deptrac\AstRunner\Event\PreCreateAstMapEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CacheableFileSubscriber implements EventSubscriberInterface
{
    private $cache;

    public function __construct(AstFileReferenceFileCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return array<string, string>
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
        $this->cache->load();
    }

    public function onPostCreateAstMapEvent(PostCreateAstMapEvent $event): void
    {
        $this->cache->write();
    }
}
