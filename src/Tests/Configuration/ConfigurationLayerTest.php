<?php

namespace DependencyTracker\Tests\Configuration;

use DependencyTracker\Configuration\ConfigurationLayer;

class ConfigurationLayerTest extends \PHPUnit_Framework_TestCase
{
    public function testFromArray()
    {
        $configurationLayer = ConfigurationLayer::fromArray([
            'name' => 'some_name',
            'collectors' => [
                ['type' => 'foo1', 'foo' => 'bar'],
                ['type' => 'foo2', 'foo' => 'bar'],
            ],
        ]);

        $this->assertEquals('some_name', $configurationLayer->getName());
        $this->assertCount(2, $configurationLayer->getCollectors());
        $this->assertEquals('foo1', $configurationLayer->getCollectors()[0]->getType());
        $this->assertEquals(['type' => 'foo1', 'foo' => 'bar'], $configurationLayer->getCollectors()[0]->getArgs());
        $this->assertEquals('foo2', $configurationLayer->getCollectors()[1]->getType());
        $this->assertEquals(['type' => 'foo2', 'foo' => 'bar'], $configurationLayer->getCollectors()[1]->getArgs());
    }
}
