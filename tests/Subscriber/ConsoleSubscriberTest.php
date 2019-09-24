<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Subscriber;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\Event\AstFileAnalyzedEvent;
use SensioLabs\Deptrac\AstRunner\Event\AstFileSyntaxErrorEvent;
use SensioLabs\Deptrac\AstRunner\Event\PostCreateAstMapEvent;
use SensioLabs\Deptrac\AstRunner\Event\PreCreateAstMapEvent;
use SensioLabs\Deptrac\Dependency\Event\PostEmitEvent;
use SensioLabs\Deptrac\Dependency\Event\PostFlattenEvent;
use SensioLabs\Deptrac\Dependency\Event\PreEmitEvent;
use SensioLabs\Deptrac\Dependency\Event\PreFlattenEvent;
use SensioLabs\Deptrac\Subscriber\ConsoleSubscriber;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleSubscriberTest extends TestCase
{
    public function testSubscribedEvents(): void
    {
        static::assertSame(
            [
                PreCreateAstMapEvent::class => 'onPreCreateAstMapEvent',
                PostCreateAstMapEvent::class => 'onPostCreateAstMapEvent',
                AstFileAnalyzedEvent::class => 'onAstFileAnalyzedEvent',
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

        static::assertSame("Start to create an AstMap for 9999999 Files.\n", $output->fetch());
    }

    public function testOnPostCreateAstMapEventWithVerboseVerbosity(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPostCreateAstMapEvent(new PostCreateAstMapEvent());

        static::assertSame("AstMap created.\n", $output->fetch());
    }

    public function testOnAstFileAnalyzedEventWithVerboseVerbosity(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onAstFileAnalyzedEvent(new AstFileAnalyzedEvent(new \SplFileInfo('foo.php')));

        static::assertSame("Parsing File foo.php\n", $output->fetch());
    }

    public function testOnAstFileSyntaxErrorEvent(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onAstFileSyntaxErrorEvent(
            new AstFileSyntaxErrorEvent(new \SplFileInfo('foo.php'), 'Invalid')
        );

        static::assertSame("\nSyntax Error on File foo.php\nInvalid\n\n", $output->fetch());
    }

    public function testOnPreDependencyEmit(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPreDependencyEmit(new PreEmitEvent('emitter-name'));

        static::assertSame("start emitting dependencies \"emitter-name\"\n", $output->fetch());
    }

    public function testOnPostDependencyEmit(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPostDependencyEmit(new PostEmitEvent());

        static::assertSame("end emitting dependencies\n", $output->fetch());
    }

    public function testOnPreDependencyFlatten(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPreDependencyFlatten(new PreFlattenEvent());

        static::assertSame("start flatten dependencies\n", $output->fetch());
    }

    public function testOnPostDependencyFlatten(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPostDependencyFlatten(new PostFlattenEvent());

        static::assertSame("end flatten dependencies\n", $output->fetch());
    }
}
