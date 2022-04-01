<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Collector;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Collector\BoolCollector;
use Qossmic\Deptrac\Collector\ClassNameCollector;
use Qossmic\Deptrac\Collector\CollectorInterface;
use Qossmic\Deptrac\Collector\Registry;

final class RegistryTest extends TestCase
{
    public function testGetCollector(): void
    {
        $fooCollector = $this->createMock(CollectorInterface::class);

        self::assertSame(
            $fooCollector,
            (new Registry(['foo' => $fooCollector]))->getCollector('foo')
        );
    }

    public function testGetUnknownCollector(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Registry([]))->getCollector('foo');
    }

    public function testGetCollectorByFQCN(): void
    {
        $classNameCollector = new ClassNameCollector();
        $registry = new Registry(['RegistryTest' => $classNameCollector]);

        self::assertSame($classNameCollector, $registry->getCollector(ClassNameCollector::class));
    }

    public function testGetUnknownCollectorByFQCN(): void
    {
        $classNameCollector = new ClassNameCollector();
        $registry = new Registry(['className' => $classNameCollector]);

        $collector = $registry->getCollector(BoolCollector::class);

        self::assertNotSame($classNameCollector, $collector);
        self::assertInstanceOf(BoolCollector::class, $collector);
    }

    public function testGetCollectorByFQCNLoadFromCache(): void
    {
        $classNameCollector = new ClassNameCollector();
        $registry = new Registry(['className' => $classNameCollector]);

        $collector = $registry->getCollector(ClassNameCollector::class);

        self::assertInstanceOf(ClassNameCollector::class, $collector);
        self::assertSame($collector, $registry->getCollector(ClassNameCollector::class));
    }
}
