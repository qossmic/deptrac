<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Configuration\ConfigurationLayer;

final class ConfigurationLayerTest extends TestCase
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

        self::assertEquals('some_name', $configurationLayer->getName());
        self::assertCount(2, $configurationLayer->getCollectors());
        self::assertEquals('foo1', $configurationLayer->getCollectors()[0]->getType());
        self::assertEquals(['type' => 'foo1', 'foo' => 'bar'], $configurationLayer->getCollectors()[0]->getArgs());
        self::assertEquals('foo2', $configurationLayer->getCollectors()[1]->getType());
        self::assertEquals(['type' => 'foo2', 'foo' => 'bar'], $configurationLayer->getCollectors()[1]->getArgs());
    }
}
