<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Layer\Collector;

use LogicException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\SuperGlobalToken;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\VariableReference;
use Qossmic\Deptrac\Core\Layer\Collector\SuperglobalCollector;

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
            new VariableReference(SuperGlobalToken::from($name))
        );

        self::assertSame($expected, $actual);
    }

    public function testWrongRegexParam(): void
    {
        $this->expectException(LogicException::class);

        $this->collector->satisfy(
            ['Foo' => 'a'],
            new VariableReference(SuperGlobalToken::from('_POST'))
        );
    }
}
