<?php

namespace Tests\SensioLabs\Deptrac;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Collector\Registry;
use SensioLabs\Deptrac\Collector\CollectorInterface;

class CollectorFactoryTest extends TestCase
{
    public function testGetCollector()
    {
        $fooCollector = $this->prophesize(CollectorInterface::class);
        $fooCollector->getType()->willReturn('foo');
        $fooCollector = $fooCollector->reveal();

        $this->assertSame(
            $fooCollector,
            (new Registry([
                $fooCollector,
            ]))->getCollector('foo')
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetUnknownCollector()
    {
        (new Registry([]))->getCollector('foo');
    }
}
