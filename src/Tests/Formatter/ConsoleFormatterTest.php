<?php

namespace SensioLabs\Deptrac\Tests\Formatter;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\Event\AstFileAnalyzedEvent;
use SensioLabs\AstRunner\Event\AstFileSyntaxErrorEvent;
use SensioLabs\AstRunner\Event\PostCreateAstMapEvent;
use SensioLabs\Deptrac\Formatter\ConsoleFormatter;
use SensioLabs\AstRunner\Event\PreCreateAstMapEvent;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\SplFileInfo;

class ConsoleFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = new EventDispatcher();
    }

    public function testOnPreCreateAstMapEvent()
    {
        $output = new BufferedOutput();
        $consoleFormatter = new ConsoleFormatter($output);
        $this->dispatcher->addSubscriber($consoleFormatter);

        $this->dispatcher->dispatch(PreCreateAstMapEvent::class, new PreCreateAstMapEvent(9999999));
        $this->assertContains('9999999', $output->fetch());
    }

    public function testOnPostCreateAstMapEvent()
    {
        $output = new BufferedOutput();
        $consoleFormatter = new ConsoleFormatter($output);
        $this->dispatcher->addSubscriber($consoleFormatter);
        $mockAstMap = $this->getMockBuilder(AstMap::class)->disableOriginalConstructor()->getMock();

        $this->dispatcher->dispatch(PostCreateAstMapEvent::class, new PostCreateAstMapEvent($mockAstMap));
        $this->assertContains('created', $output->fetch());
    }

    public function testOnAstFileAnalyzedEventWithVerboseVerbosity()
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);
        $consoleFormatter = new ConsoleFormatter($output);
        $this->dispatcher->addSubscriber($consoleFormatter);

        $this->dispatcher->dispatch(AstFileAnalyzedEvent::class, new AstFileAnalyzedEvent(new SplFileInfo('t', '.', 'tempfile.txt')));
        $this->assertContains('tempfile.txt', $output->fetch());
    }

    public function testOnAstFileAnalyzedEventWithDefaultVerbosity()
    {
        $output = new BufferedOutput();
        $consoleFormatter = new ConsoleFormatter($output);
        $this->dispatcher->addSubscriber($consoleFormatter);

        $this->dispatcher->dispatch(AstFileAnalyzedEvent::class, new AstFileAnalyzedEvent(new SplFileInfo('t', '.', 'tempfile.txt')));
        $this->assertEquals('.', $output->fetch());
    }

    public function testAstFileSyntaxErrorEvent()
    {
        $output = new BufferedOutput();
        $consoleFormatter = new ConsoleFormatter($output);
        $this->dispatcher->addSubscriber($consoleFormatter);

        $this->dispatcher->dispatch(
            AstFileSyntaxErrorEvent::class,
            new AstFileSyntaxErrorEvent(
                new SplFileInfo('t', '.', 'tempfile.txt'),
                'specificsyntaxerror'
            )
        );

        $outputContents = $output->fetch();
        $this->assertContains('tempfile.txt', $outputContents);
        $this->assertContains('specificsyntaxerror', $outputContents);
    }
}
