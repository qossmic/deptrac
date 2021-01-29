<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\OutputFormatter\OutputFormatterOption;

final class OutputFormatterOptionTest extends TestCase
{
    public function testGetSet(): void
    {
        $formatterOption = OutputFormatterOption::newValueOption('name', 'desc', 'default');
        self::assertEquals('name', $formatterOption->getName());
        self::assertEquals(4, $formatterOption->getMode());
        self::assertEquals('desc', $formatterOption->getDescription());
        self::assertEquals('default', $formatterOption->getDefault());
    }
}
