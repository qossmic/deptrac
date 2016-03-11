<?php


namespace SensioLabs\Deptrac\Tests;


use SensioLabs\Deptrac\OutputFormatterFactory;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInterface;

class OutputFormatterFactoryTest extends \PHPUnit_Framework_TestCase
{

    private function createNamedFormatter($name)
    {
        $formatter = $this->prophesize(OutputFormatterInterface::class);
        $formatter->getName()->willReturn($name);

        return $formatter->reveal();
    }

    public function testGetFormatterByName()
    {
        $formatterFactory = new OutputFormatterFactory([
            $formatter1 = $this->createNamedFormatter('formatter1'),
            $formatter2 = $this->createNamedFormatter('formatter2')
        ]);

        $this->assertSame($formatter1, $formatterFactory->getFormatterByName('formatter1'));
        $this->assertSame($formatter2, $formatterFactory->getFormatterByName('formatter2'));
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetFormatterByNameNotFound()
    {
        (new OutputFormatterFactory([]))->getFormatterByName('formatter1');
    }

}
