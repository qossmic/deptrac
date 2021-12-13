<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;
use Qossmic\Deptrac\Collector\BoolCollector;
use Qossmic\Deptrac\Collector\CollectorInterface;
use Qossmic\Deptrac\Collector\LayerCollector;
use Qossmic\Deptrac\Collector\Registry;

final class LayerCollectorTest extends TestCase
{
    public function testSatisfy(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('LayerCollector needs the layer configuration');

        (new LayerCollector())->satisfy(
            [],
            $this->createMock(AstClassReference::class),
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );
    }

    public function testType(): void
    {
        self::assertEquals('layer', (new LayerCollector())->getType());
    }

}
