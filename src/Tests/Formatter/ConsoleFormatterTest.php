<?php

namespace DependencyTracker\Tests\Formatter;

use DependencyTracker\Formatter\ConsoleFormatter;
use SensioLabs\AstRunner\Event\PreCreateAstMapEvent;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ConsoleFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testOnPreCreateAstMapEvent()
    {
        (new ConsoleFormatter(
            $dispatcher = new EventDispatcher(),
            $formatter = new BufferedOutput()
        ));

        $dispatcher->dispatch(PreCreateAstMapEvent::class, new PreCreateAstMapEvent(9999999));
        $this->assertContains('9999999', $formatter->fetch());
    }
}
