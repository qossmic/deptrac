<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Subscriber;

use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\CacheableFileParser;
use SensioLabs\Deptrac\AstRunner\Event\PostCreateAstMapEvent;
use SensioLabs\Deptrac\AstRunner\Event\PreCreateAstMapEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CacheableFileSubscriber implements EventSubscriberInterface
{
    private $cacheableFileParser;

    public function __construct(CacheableFileParser $cacheableFileParser)
    {
        $this->cacheableFileParser = $cacheableFileParser;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PreCreateAstMapEvent::class => 'onPreCreateAstMapEvent',
            PostCreateAstMapEvent::class => 'onPostCreateAstMapEvent',
        ];
    }

    public function onPreCreateAstMapEvent(PreCreateAstMapEvent $event): void
    {
        $this->cacheableFileParser->load();
    }

    public function onPostCreateAstMapEvent(PostCreateAstMapEvent $event): void
    {
        $this->cacheableFileParser->write();
    }
}
