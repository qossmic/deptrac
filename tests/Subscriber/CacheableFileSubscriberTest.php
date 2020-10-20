<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Subscriber;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceFileCache;
use SensioLabs\Deptrac\AstRunner\Event\PostCreateAstMapEvent;
use SensioLabs\Deptrac\AstRunner\Event\PreCreateAstMapEvent;
use SensioLabs\Deptrac\Subscriber\CacheableFileSubscriber;

final class CacheableFileSubscriberTest extends TestCase
{
    public function testSubscribedEvents(): void
    {
        static::assertSame(
            [
                PreCreateAstMapEvent::class => 'onPreCreateAstMapEvent',
                PostCreateAstMapEvent::class => 'onPostCreateAstMapEvent',
            ],
            CacheableFileSubscriber::getSubscribedEvents()
        );
    }

    public function testOnPreCreateAstMapEvent(): void
    {
        $cache = $this->createMock(AstFileReferenceFileCache::class);
        $cache->expects(static::once())->method('load');

        (new CacheableFileSubscriber($cache))->onPreCreateAstMapEvent(new PreCreateAstMapEvent(1));
    }

    public function testOnPostCreateAstMapEvent(): void
    {
        $cache = $this->createMock(AstFileReferenceFileCache::class);
        $cache->expects(static::once())->method('write');

        (new CacheableFileSubscriber($cache))->onPostCreateAstMapEvent(new PostCreateAstMapEvent());
    }
}
