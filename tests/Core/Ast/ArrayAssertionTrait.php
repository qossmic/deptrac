<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Ast;

trait ArrayAssertionTrait
{
    public static function assertArrayValuesEquals(array $expected, array $value): void
    {
        $expected = array_values($expected);
        $value = array_values($value);

        sort($expected);
        sort($value);

        static::assertSame($expected, $value);
    }
}
