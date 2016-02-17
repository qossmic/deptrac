<?php


namespace DependencyTracker\Tests;


use DependencyTracker\Collector\CollectorInterface;
use DependencyTracker\CollectorFactory;

class CollectorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCollector()
    {
        $fooCollector = $this->prophesize(CollectorInterface::class);
        $fooCollector->getType()->willReturn('foo');
        $fooCollector = $fooCollector->reveal();


        $this->assertSame(
            $fooCollector,
            (new CollectorFactory([
                $fooCollector
            ]))->getCollector('foo')
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetUnknownCollector()
    {
        (new CollectorFactory([]))->getCollector('foo');
    }

}
