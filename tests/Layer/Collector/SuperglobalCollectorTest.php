<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Layer\Collector;

use LogicException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\Variable\SuperGlobalToken;
use Qossmic\Deptrac\Ast\AstMap\Variable\VariableReference;
use Qossmic\Deptrac\Layer\Collector\SuperglobalCollector;

final class SuperglobalCollectorTest extends TestCase
{
    private SuperglobalCollector $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collector = new SuperglobalCollector();
    }

    public function provideSatisfy(): iterable
    {
        yield [['value' => ['_GET', '_SESSION']], '_GET', true];
        yield [['value' => ['_COOKIE']], '_POST', false];
    }

    /**
     * @dataProvider provideSatisfy
     */
    public function testSatisfy(array $configuration, string $name, bool $expected): void
    {
        $actual = $this->collector->satisfy(
            $configuration,
            new VariableReference(new SuperGlobalToken($name)),
            new AstMap([])
        );

        self::assertSame($expected, $actual);
    }

    public function testWrongRegexParam(): void
    {
        $this->expectException(LogicException::class);

        $this->collector->satisfy(
            ['Foo' => 'a'],
            new VariableReference(new SuperGlobalToken('_POST')),
            new AstMap([])
        );
    }
}
