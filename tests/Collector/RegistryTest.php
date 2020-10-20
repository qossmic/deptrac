<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Collector\BoolCollector;
use SensioLabs\Deptrac\Collector\ClassNameCollector;
use SensioLabs\Deptrac\Collector\CollectorInterface;
use SensioLabs\Deptrac\Collector\Registry;

final class RegistryTest extends TestCase
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

    public function testGetCollectorByFQCN(): void
    {
        $classNameCollector = new ClassNameCollector();
        $registry = new Registry([$classNameCollector]);

        static::assertSame($classNameCollector, $registry->getCollector(ClassNameCollector::class));
    }

    public function testGetUnknownCollectorByFQCN(): void
    {
        $classNameCollector = new ClassNameCollector();
        $registry = new Registry([$classNameCollector]);

        $collector = $registry->getCollector(BoolCollector::class);

        static::assertNotSame($classNameCollector, $collector);
        static::assertInstanceOf(BoolCollector::class, $collector);
    }

    public function testGetCollectorByFQCNLoadFromCache(): void
    {
        $classNameCollector = new ClassNameCollector();
        $registry = new Registry([$classNameCollector]);

        $collector = $registry->getCollector(ClassNameCollector::class);

        static::assertInstanceOf(ClassNameCollector::class, $collector);
        static::assertSame($collector, $registry->getCollector(ClassNameCollector::class));
    }

    public function testGetUnknownCollectorByFQCNAndThenByTypeFromCache(): void
    {
        $registry = new Registry([]);

        $collector = $registry->getCollector(ClassNameCollector::class);

        static::assertInstanceOf(ClassNameCollector::class, $collector);
        static::assertSame($collector, $registry->getCollector('className'));
    }
}
