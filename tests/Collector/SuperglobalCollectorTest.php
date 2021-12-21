<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Collector;

use LogicException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\Collector\Registry;
use Qossmic\Deptrac\Collector\SuperglobalCollector;

final class SuperglobalCollectorTest extends TestCase
{
    public function dataProviderSatisfy(): iterable
    {
        yield [['names' => ['_GET', '_SESSION']], '_GET', true];
        yield [['names' => ['_COOKIE']], '_POST', false];
    }

    public function testType(): void
    {
        self::assertEquals('superglobal', (new SuperglobalCollector())->getType());
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, string $name, bool $expected): void
    {
        $stat = (new SuperglobalCollector())->satisfy(
            $configuration,
            new AstMap\AstVariableReference(new AstMap\SuperGlobalName($name)),
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );

        self::assertEquals($expected, $stat);
    }

    public function testWrongRegexParam(): void
    {
        $this->expectException(LogicException::class);

        (new SuperglobalCollector())->satisfy(
            ['Foo' => 'a'],
            new AstMap\AstVariableReference(new AstMap\SuperGlobalName('_POST')),
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );
    }
}
