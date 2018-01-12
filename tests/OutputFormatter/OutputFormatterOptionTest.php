<?php

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterOption;

class OutputFormatterOptionTest extends TestCase
{
    public function testGetSet()
    {
        $formatterOption = OutputFormatterOption::newValueOption('name', 'desc', 'default');
        $this->assertEquals('name', $formatterOption->getName());
        $this->assertEquals(4, $formatterOption->getMode());
        $this->assertEquals('desc', $formatterOption->getDescription());
        $this->assertEquals('default', $formatterOption->getDefault());
    }
}
