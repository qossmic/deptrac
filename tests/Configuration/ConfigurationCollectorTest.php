<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Configuration\ConfigurationCollector;

final class ConfigurationCollectorTest extends TestCase
{
    public function testInvalidFromArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ConfigurationCollector::fromArray([]);
    }

    public function testFromArray(): void
    {
        $configurationCollector = ConfigurationCollector::fromArray($args = [
            'type' => 'foo',
            'abc' => 'def',
        ]);

        self::assertEquals('foo', $configurationCollector->getType());
        self::assertEquals($args, $configurationCollector->getArgs());
    }
}
