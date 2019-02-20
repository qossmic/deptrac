<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Collector\CollectorInterface;
use SensioLabs\Deptrac\Collector\Registry;

class RegistryTest extends TestCase
{
    public function testGetCollector(): void
    {
        $fooCollector = $this->prophesize(CollectorInterface::class);
        $fooCollector->getType()->willReturn('foo');
        $fooCollector = $fooCollector->reveal();

        static::assertSame(
            $fooCollector,
            (new Registry([
                $fooCollector,
            ]))->getCollector('foo')
        );
    }

    public function testGetUnknownCollector(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Registry([]))->getCollector('foo');
    }
}
