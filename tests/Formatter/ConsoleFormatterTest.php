<?php

namespace Tests\SensioLabs\Deptrac\Formatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\AstRunner\Event\PreCreateAstMapEvent;
use SensioLabs\Deptrac\Formatter\ConsoleFormatter;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ConsoleFormatterTest extends TestCase
{
    public function testOnPreCreateAstMapEventWithVerboseVerbosity()
    {
        (new ConsoleFormatter(
            $dispatcher = new EventDispatcher(),
            $formatter = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE)
        ));

        $dispatcher->dispatch(PreCreateAstMapEvent::class, new PreCreateAstMapEvent(9999999));
        $this->assertContains('9999999', $formatter->fetch());
    }

    public function testOnPreCreateAstMapEventWithDefaultVerbosity()
    {
        (new ConsoleFormatter(
            $dispatcher = new EventDispatcher(),
            $formatter = new BufferedOutput()
        ));

        $dispatcher->dispatch(PreCreateAstMapEvent::class, new PreCreateAstMapEvent(9999999));
        $this->assertContains('.', $formatter->fetch());
    }
}
