<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;

class OutputFormatterInputTest extends TestCase
{
    public function testGetOption(): void
    {
        static::assertEquals('b', (new OutputFormatterInput(['a' => 'b']))->getOption('a'));
    }

    public function testGetOptionException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new OutputFormatterInput(['a' => 'b']))->getOption('c');
    }
}
