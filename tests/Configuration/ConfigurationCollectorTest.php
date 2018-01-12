<?php

namespace Tests\SensioLabs\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Configuration\ConfigurationCollector;

class ConfigurationCollectorTest extends TestCase
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
            'abc' => 'def',
        ]);

        $this->assertEquals('foo', $configurationCollector->getType());
        $this->assertEquals($args, $configurationCollector->getArgs());
    }
}
