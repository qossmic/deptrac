<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Subscriber;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\Event\AstFileAnalysedEvent;
use Qossmic\Deptrac\AstRunner\Event\AstFileSyntaxErrorEvent;
use Qossmic\Deptrac\AstRunner\Event\PostCreateAstMapEvent;
use Qossmic\Deptrac\AstRunner\Event\PreCreateAstMapEvent;
use Qossmic\Deptrac\Dependency\Event\PostEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PostFlattenEvent;
use Qossmic\Deptrac\Dependency\Event\PreEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PreFlattenEvent;
use Qossmic\Deptrac\Subscriber\ConsoleSubscriber;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleSubscriberTest extends TestCase
{
    public function testSubscribedEvents(): void
    {
        self::assertSame(
            [
                PreCreateAstMapEvent::class => 'onPreCreateAstMapEvent',
                PostCreateAstMapEvent::class => 'onPostCreateAstMapEvent',
                AstFileAnalysedEvent::class => 'onAstFileAnalysedEvent',
                AstFileSyntaxErrorEvent::class => 'onAstFileSyntaxErrorEvent',
                PreEmitEvent::class => 'onPreDependencyEmit',
                PostEmitEvent::class => 'onPostDependencyEmit',
                PreFlattenEvent::class => 'onPreDependencyFlatten',
                PostFlattenEvent::class => 'onPostDependencyFlatten',
            ],
            ConsoleSubscriber::getSubscribedEvents()
        );
    }

    public function testOnPreCreateAstMapEventWithVerboseVerbosity(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPreCreateAstMapEvent(new PreCreateAstMapEvent(9999999));

        self::assertSame("Start to create an AstMap for 9999999 Files.\n", $output->fetch());
    }

    public function testOnPostCreateAstMapEventWithVerboseVerbosity(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPostCreateAstMapEvent(new PostCreateAstMapEvent());

        self::assertSame("AstMap created.\n", $output->fetch());
    }

    public function testOnAstFileAnalysedEventWithVerboseVerbosity(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onAstFileAnalysedEvent(new AstFileAnalysedEvent('foo.php'));

        self::assertSame("Parsing File foo.php\n", $output->fetch());
    }

    public function testOnAstFileSyntaxErrorEvent(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onAstFileSyntaxErrorEvent(
            new AstFileSyntaxErrorEvent('foo.php', 'Invalid')
        );

        self::assertSame("\nSyntax Error on File foo.php\nInvalid\n\n", $output->fetch());
    }

    public function testOnPreDependencyEmit(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPreDependencyEmit(new PreEmitEvent('emitter-name'));

        self::assertSame("start emitting dependencies \"emitter-name\"\n", $output->fetch());
    }

    public function testOnPostDependencyEmit(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPostDependencyEmit(new PostEmitEvent());

        self::assertSame("end emitting dependencies\n", $output->fetch());
    }

    public function testOnPreDependencyFlatten(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPreDependencyFlatten(new PreFlattenEvent());

        self::assertSame("start flatten dependencies\n", $output->fetch());
    }

    public function testOnPostDependencyFlatten(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPostDependencyFlatten(new PostFlattenEvent());

        self::assertSame("end flatten dependencies\n", $output->fetch());
    }
}
