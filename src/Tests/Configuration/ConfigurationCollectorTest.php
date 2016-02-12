<?php


namespace DependencyTracker\Tests\Configuration;


use DependencyTracker\Configuration\ConfigurationCollector;

class ConfigurationCollectorTest extends \PHPUnit_Framework_TestCase
{

    /** @expectedException \InvalidArgumentException */
    public function testInvalidFromArray()
    {
        ConfigurationCollector::fromArray([]);
    }

    public function testFromArray()
    {
        $configurationCollector = ConfigurationCollector::fromArray($args = [
            'type' => 'foo',
            'abc' => 'def'
        ]);

        $this->assertEquals('foo', $configurationCollector->getType());
        $this->assertEquals($args, $configurationCollector->getArgs());
    }

}
