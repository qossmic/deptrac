<?php

namespace SensioLabs\Deptrac\Tests\OutputFormatter;

use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;

class OutputFormatterInputTest extends \PHPUnit_Framework_TestCase
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
