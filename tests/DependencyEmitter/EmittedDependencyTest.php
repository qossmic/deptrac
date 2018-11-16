<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\DependencyEmitter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\DependencyEmitter\EmittedDependency;

class EmittedDependencyTest extends TestCase
{
    public function testGet(): void
    {
        $dep = new EmittedDependency('class', 3, 'type');
        static::assertSame('class', $dep->getClass());
        static::assertSame(3, $dep->getLine());
        static::assertSame('type', $dep->getType());
    }
}
