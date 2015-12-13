<?php


namespace DependencyTracker\Tests;


use DependencyTracker\Collector\CollectorInterface;
use DependencyTracker\CollectorFactory;

class CollectorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function getCollectorTest()
    {
        $fooCollector = $this->prophesize(CollectorInterface::class)->reveal();

        $this->assertSame(
            $fooCollector,
            (new CollectorFactory([
                'foo' => $fooCollector
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
