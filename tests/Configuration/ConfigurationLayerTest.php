<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Configuration\ConfigurationLayer;

class ConfigurationLayerTest extends TestCase
{
    public function testFromArray(): void
    {
        $configurationLayer = ConfigurationLayer::fromArray([
            'name' => 'some_name',
            'collectors' => [
                ['type' => 'foo1', 'foo' => 'bar'],
                ['type' => 'foo2', 'foo' => 'bar'],
            ],
        ]);

        static::assertEquals('some_name', $configurationLayer->getName());
        static::assertCount(2, $configurationLayer->getCollectors());
        static::assertEquals('foo1', $configurationLayer->getCollectors()[0]->getType());
        static::assertEquals(['type' => 'foo1', 'foo' => 'bar'], $configurationLayer->getCollectors()[0]->getArgs());
        static::assertEquals('foo2', $configurationLayer->getCollectors()[1]->getType());
        static::assertEquals(['type' => 'foo2', 'foo' => 'bar'], $configurationLayer->getCollectors()[1]->getArgs());
    }
}
