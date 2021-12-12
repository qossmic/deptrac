<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Subscriber;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\Event\AstFileAnalysedEvent;
use Qossmic\Deptrac\AstRunner\Event\AstFileSyntaxErrorEvent;
use Qossmic\Deptrac\AstRunner\Event\PostCreateAstMapEvent;
use Qossmic\Deptrac\AstRunner\Event\PreCreateAstMapEvent;
use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Dependency\Event\PostEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PostFlattenEvent;
use Qossmic\Deptrac\Dependency\Event\PreEmitEvent;
use Qossmic\Deptrac\Dependency\Event\PreFlattenEvent;
use Qossmic\Deptrac\Subscriber\ConsoleSubscriber;
use staabm\PHPUnitCrossOs\Comparator\EolAgnosticString;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tests\Qossmic\Deptrac\CrossOsAgnosticEqualsTrait;

final class ConsoleSubscriberTest extends TestCase
{
    use CrossOsAgnosticEqualsTrait;
    
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
        $symfonyOutput = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);
        $output = new SymfonyOutput($symfonyOutput, new Style(new SymfonyStyle(new ArrayInput([]), $symfonyOutput)));

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPreCreateAstMapEvent(new PreCreateAstMapEvent(9999999));

        self::assertEquals(new EolAgnosticString("Start to create an AstMap for 9999999 Files.\n"), $symfonyOutput->fetch());
    }

    public function testOnPostCreateAstMapEventWithVerboseVerbosity(): void
    {
        $symfonyOutput = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);
        $output = new SymfonyOutput($symfonyOutput, new Style(new SymfonyStyle(new ArrayInput([]), $symfonyOutput)));

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPostCreateAstMapEvent(new PostCreateAstMapEvent());

        self::assertEquals(new EolAgnosticString("AstMap created.\n"), $symfonyOutput->fetch());
    }

    public function testOnAstFileAnalysedEventWithVerboseVerbosity(): void
    {
        $symfonyOutput = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);
        $output = new SymfonyOutput($symfonyOutput, new Style(new SymfonyStyle(new ArrayInput([]), $symfonyOutput)));

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onAstFileAnalysedEvent(new AstFileAnalysedEvent('foo.php'));

        self::assertEquals(new EolAgnosticString("Parsing File foo.php\n"), $symfonyOutput->fetch());
    }

    public function testOnAstFileSyntaxErrorEvent(): void
    {
        $symfonyOutput = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);
        $output = new SymfonyOutput($symfonyOutput, new Style(new SymfonyStyle(new ArrayInput([]), $symfonyOutput)));

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onAstFileSyntaxErrorEvent(
            new AstFileSyntaxErrorEvent('foo.php', 'Invalid')
        );

        self::assertEquals(new EolAgnosticString("\nSyntax Error on File foo.php\nInvalid\n\n"), $symfonyOutput->fetch());
    }

    public function testOnPreDependencyEmit(): void
    {
        $symfonyOutput = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);
        $output = new SymfonyOutput($symfonyOutput, new Style(new SymfonyStyle(new ArrayInput([]), $symfonyOutput)));

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPreDependencyEmit(new PreEmitEvent('emitter-name'));

        self::assertEquals(new EolAgnosticString("start emitting dependencies \"emitter-name\"\n"), $symfonyOutput->fetch());
    }

    public function testOnPostDependencyEmit(): void
    {
        $symfonyOutput = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);
        $output = new SymfonyOutput($symfonyOutput, new Style(new SymfonyStyle(new ArrayInput([]), $symfonyOutput)));

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPostDependencyEmit(new PostEmitEvent());

        self::assertEquals(new EolAgnosticString("end emitting dependencies\n"), $symfonyOutput->fetch());
    }

    public function testOnPreDependencyFlatten(): void
    {
        $symfonyOutput = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);
        $output = new SymfonyOutput($symfonyOutput, new Style(new SymfonyStyle(new ArrayInput([]), $symfonyOutput)));

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPreDependencyFlatten(new PreFlattenEvent());

        self::assertEquals(new EolAgnosticString("start flatten dependencies\n"), $symfonyOutput->fetch());
    }

    public function testOnPostDependencyFlatten(): void
    {
        $symfonyOutput = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);
        $output = new SymfonyOutput($symfonyOutput, new Style(new SymfonyStyle(new ArrayInput([]), $symfonyOutput)));

        $subscriber = new ConsoleSubscriber($output);
        $subscriber->onPostDependencyFlatten(new PostFlattenEvent());

        self::assertEquals(new EolAgnosticString("end flatten dependencies\n"), $symfonyOutput->fetch());
    }
}
