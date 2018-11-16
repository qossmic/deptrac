<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Configuration\ConfigurationCollector;

class ConfigurationCollectorTest extends TestCase
{
    /** @expectedException \InvalidArgumentException */
    public function testInvalidFromArray(): void
    {
        ConfigurationCollector::fromArray([]);
    }

    public function testFromArray(): void
    {
        $configurationCollector = ConfigurationCollector::fromArray($args = [
            'type' => 'foo',
            'abc' => 'def',
        ]);

        static::assertEquals('foo', $configurationCollector->getType());
        static::assertEquals($args, $configurationCollector->getArgs());
    }
}
