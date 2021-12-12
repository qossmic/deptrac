<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Subscriber;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\Event\AstFileAnalysedEvent;
use Qossmic\Deptrac\AstRunner\Event\PostCreateAstMapEvent;
use Qossmic\Deptrac\AstRunner\Event\PreCreateAstMapEvent;
use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Subscriber\ProgressSubscriber;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ProgressSubscriberTest extends TestCase
{
    public function testSubscribedEvents(): void
    {
        self::assertSame(
            [
                PreCreateAstMapEvent::class => 'onPreCreateAstMapEvent',
                PostCreateAstMapEvent::class => ['onPostCreateAstMapEvent', 1],
                AstFileAnalysedEvent::class => 'onAstFileAnalysedEvent',
            ],
            ProgressSubscriber::getSubscribedEvents()
        );
    }

    public function testProgress(): void
    {
        $bufferedOutput = new BufferedOutput();
        $subscriber = new ProgressSubscriber($this->createSymfonyOutput($bufferedOutput));

        $subscriber->onPreCreateAstMapEvent(new PreCreateAstMapEvent(1));
        $subscriber->onAstFileAnalysedEvent(new AstFileAnalysedEvent('foo.php'));
        $subscriber->onPostCreateAstMapEvent(new PostCreateAstMapEvent());

        $expectedOutput = <<<OUT
 0/1 [░░░░░░░░░░░░░░░░░░░░░░░░░░░░]   0%
 1/1 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%


OUT;
        if ("\\" === DIRECTORY_SEPARATOR) {
            $expectedOutput = <<<OUT
 0/1 [>---------------------------]   0%
 1/1 [============================] 100%


OUT;
            $expectedOutput = str_replace("\n", PHP_EOL, $expectedOutput);
        }

        self::assertSame($expectedOutput, $bufferedOutput->fetch());
    }

    public function testOnPostCreateAstMapEvent(): void
    {
        $formatter = new BufferedOutput();
        $subscriber = new ProgressSubscriber($this->createSymfonyOutput($formatter));

        $subscriber->onPreCreateAstMapEvent(new PreCreateAstMapEvent(1));
        $subscriber->onPostCreateAstMapEvent(new PostCreateAstMapEvent());

        $expectedOutput = <<<OUT
 0/1 [░░░░░░░░░░░░░░░░░░░░░░░░░░░░]   0%
 1/1 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%


OUT;

        if ("\\" === DIRECTORY_SEPARATOR) {
            $expectedOutput = <<<OUT
 0/1 [>---------------------------]   0%
 1/1 [============================] 100%


OUT;
            $expectedOutput = str_replace("\n", PHP_EOL, $expectedOutput);
        }

        self::assertSame($expectedOutput, $formatter->fetch());
    }

    private function createSymfonyOutput(BufferedOutput $bufferedOutput): SymfonyOutput
    {
        return new SymfonyOutput(
            $bufferedOutput,
            new Style(new SymfonyStyle($this->createMock(InputInterface::class), $bufferedOutput))
        );
    }
}
