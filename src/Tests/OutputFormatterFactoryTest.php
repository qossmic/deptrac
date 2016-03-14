<?php


namespace SensioLabs\Deptrac\Tests;


use SensioLabs\Deptrac\OutputFormatter\OutputFormatterOption;
use SensioLabs\Deptrac\OutputFormatterFactory;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

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

    public function testGetFormatterOptions()
    {
        $formatter1 = $this->prophesize(OutputFormatterInterface::class);
        $formatter1->getName()->willReturn('foo1');
        $formatter1->configureOptions()->willReturn([
            OutputFormatterOption::newValueOption('f1-n1', 'f1-n1-desc', 'f1-n1-default')
        ]);

        $formatter2 = $this->prophesize(OutputFormatterInterface::class);
        $formatter2->getName()->willReturn('foo2');
        $formatter2->configureOptions()->willReturn([
            OutputFormatterOption::newValueOption('f2-n1', 'f2-n1-desc', 'f2-n1-default'),
            OutputFormatterOption::newValueOption('f2-n2', 'f2-n2-desc', 'f2-n2-default')
        ]);

        $formatter3 = $this->prophesize(OutputFormatterInterface::class);
        $formatter3->getName()->willReturn('foo3');
        $formatter3->configureOptions()->willReturn([]);


        $formatterFactory = new OutputFormatterFactory([
            $formatter1->reveal(),
            $formatter2->reveal(),
            $formatter3->reveal()
        ]);

        /** @var $arguments InputArgument[] */
        $arguments = $formatterFactory->getFormatterOptions('foo1');

        $this->assertEquals('formatter-foo1',           $arguments[0]->getName());

        $this->assertEquals('formatter-foo1-f1-n1',     $arguments[1]->getName());
        $this->assertEquals('f1-n1-default',            $arguments[1]->getDefault());
        $this->assertEquals('f1-n1-desc',               $arguments[1]->getDescription());

        $this->assertEquals('formatter-foo2',           $arguments[2]->getName());

        $this->assertEquals('formatter-foo2-f2-n1',     $arguments[3]->getName());
        $this->assertEquals('f2-n1-default',            $arguments[3]->getDefault());
        $this->assertEquals('f2-n1-desc',               $arguments[3]->getDescription());

        $this->assertEquals('formatter-foo2-f2-n2',     $arguments[4]->getName());
        $this->assertEquals('f2-n2-default',            $arguments[4]->getDefault());
        $this->assertEquals('f2-n2-desc',               $arguments[4]->getDescription());


        $this->assertCount(6, $arguments);
    }

    public function testIsFormatterActive()
    {
        $formatter = (new OutputFormatterFactory([
            $this->createNamedFormatter('f1'),
            $this->createNamedFormatter('f2'),
            $this->createNamedFormatter('f3')
        ]));

        $input = $this->prophesize(InputInterface::class);
        $input->getOption('formatter-f1')->willReturn(true);
        $input->getOption('formatter-f2')->willReturn(true);
        $input->getOption('formatter-f3')->willReturn(false);

        $this->assertCount(2, $formatter->getActiveFormatters($input->reveal()));
    }

    public function testGetOutputFormatterInput()
    {
        $formatter = (new OutputFormatterFactory([
            $f1 = $this->createNamedFormatter('f1'),
            $f2 = $this->createNamedFormatter('f2'),
            $f3 = $this->createNamedFormatter('f3')
        ]));

        $input = $this->prophesize(InputInterface::class);
        $input->getOptions()->willReturn([
            'formatter-f1-lalelu' => 'jupp',
            'formatter-f3' => ''
        ]);

        $this->assertEquals('jupp', $formatter->getOutputFormatterInput($f1, $input->reveal())->getOption('lalelu'));

        try {
            $formatter->getOutputFormatterInput($f2, $input->reveal())->getOption('lalelu');
            $this->fail('expected exception');
        } catch (\InvalidArgumentException $e) {}
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetFormatterByNameNotFound()
    {
        (new OutputFormatterFactory([]))->getFormatterByName('formatter1');
    }

}
