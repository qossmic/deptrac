<?php

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;

class OutputFormatterInputTest extends TestCase
{
    public function testGetOption()
    {
        $this->assertEquals('b', (new OutputFormatterInput(['a' => 'b']))->getOption('a'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetOptionException()
    {
        (new OutputFormatterInput(['a' => 'b']))->getOption('c');
    }
}
