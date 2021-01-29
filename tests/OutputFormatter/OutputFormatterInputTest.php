<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\OutputFormatter\OutputFormatterInput;

final class OutputFormatterInputTest extends TestCase
{
    public function testGetOption(): void
    {
        self::assertEquals('b', (new OutputFormatterInput(['a' => 'b']))->getOption('a'));
    }

    public function testGetOptionAsBoolean(): void
    {
        self::assertTrue((new OutputFormatterInput(['a' => '1']))->getOptionAsBoolean('a'));
        self::assertFalse((new OutputFormatterInput(['a' => '0']))->getOptionAsBoolean('a'));
    }

    public function testGetOptionException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new OutputFormatterInput(['a' => 'b']))->getOption('c');
    }
}
