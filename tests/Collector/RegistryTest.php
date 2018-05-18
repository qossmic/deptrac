<?php

namespace Tests\SensioLabs\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Collector\CollectorInterface;
use SensioLabs\Deptrac\Collector\Registry;

class RegistryTest extends TestCase
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
