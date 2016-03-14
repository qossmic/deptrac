<?php


namespace SensioLabs\Deptrac\Tests\OutputFormatter;


use SensioLabs\Deptrac\OutputFormatter\OutputFormatterOption;

class OutputFormatterOptionTest extends \PHPUnit_Framework_TestCase
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
