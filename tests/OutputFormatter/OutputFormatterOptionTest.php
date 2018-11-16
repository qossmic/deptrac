<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterOption;

class OutputFormatterOptionTest extends TestCase
{
    public function testGetSet(): void
    {
        $formatterOption = OutputFormatterOption::newValueOption('name', 'desc', 'default');
        static::assertEquals('name', $formatterOption->getName());
        static::assertEquals(4, $formatterOption->getMode());
        static::assertEquals('desc', $formatterOption->getDescription());
        static::assertEquals('default', $formatterOption->getDefault());
    }
}
