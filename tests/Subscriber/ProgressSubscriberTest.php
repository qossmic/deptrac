<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Subscriber;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\Event\AstFileAnalyzedEvent;
use SensioLabs\Deptrac\AstRunner\Event\PostCreateAstMapEvent;
use SensioLabs\Deptrac\AstRunner\Event\PreCreateAstMapEvent;
use SensioLabs\Deptrac\Subscriber\ProgressSubscriber;
use Symfony\Component\Console\Output\BufferedOutput;

class ProgressSubscriberTest extends TestCase
{
    public function testSubscribedEvents(): void
    {
        static::assertSame(
            [
                PreCreateAstMapEvent::class => 'onPreCreateAstMapEvent',
                PostCreateAstMapEvent::class => ['onPostCreateAstMapEvent', 1],
                AstFileAnalyzedEvent::class => 'onAstFileAnalyzedEvent',
            ],
            ProgressSubscriber::getSubscribedEvents()
        );
    }

    public function testProgress(): void
    {
        $formatter = new BufferedOutput();
        $subscriber = new ProgressSubscriber($formatter);

        $subscriber->onPreCreateAstMapEvent(new PreCreateAstMapEvent(1));
        $subscriber->onAstFileAnalyzedEvent(new AstFileAnalyzedEvent(new \SplFileInfo('foo.php')));
        $subscriber->onPostCreateAstMapEvent(new PostCreateAstMapEvent());

        $expectedOutput = <<<OUT
 0/1 [>---------------------------]   0%
 1/1 [============================] 100%

OUT;

        static::assertSame($expectedOutput, $formatter->fetch());
    }

    public function testOnAstFileAnalyzedEvent(): void
    {
        $formatter = new BufferedOutput();
        $subscriber = new ProgressSubscriber($formatter);

        $subscriber->onAstFileAnalyzedEvent(new AstFileAnalyzedEvent(new \SplFileInfo('foo.php')));

        $expectedOutput = <<<OUT

    1 [->--------------------------]
OUT;

        static::assertSame($expectedOutput, $formatter->fetch());
    }

    public function testOnPostCreateAstMapEvent(): void
    {
        $formatter = new BufferedOutput();
        $subscriber = new ProgressSubscriber($formatter);

        $subscriber->onPreCreateAstMapEvent(new PreCreateAstMapEvent(1));
        $subscriber->onPostCreateAstMapEvent(new PostCreateAstMapEvent());

        $expectedOutput = <<<OUT
 0/1 [>---------------------------]   0%
 1/1 [============================] 100%

OUT;

        static::assertSame($expectedOutput, $formatter->fetch());
    }
}
