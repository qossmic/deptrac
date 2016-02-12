<?php


namespace DependencyTracker\Tests\Collector;


use DependencyTracker\Collector\ClassNameCollector;
use DependencyTracker\CollectorFactory;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;

class ClassNameCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function dataProviderStatisfy()
    {
        yield [['regex' => 'a'], 'foo\bar', true];
        yield [['regex' => 'a'], 'foo\bbr', false];
    }

    /**
     * @dataProvider dataProviderStatisfy
     */
    public function testStatisfy($configuration, $className, $expected)
    {
        $astClassReference = $this->prophesize(AstClassReferenceInterface::class);
        $astClassReference->getClassName()->willReturn($className);


        $stat = (new ClassNameCollector())->satisfy(
            $configuration,
            $astClassReference->reveal(),
            $this->prophesize(AstMap::class)->reveal(),
            $this->prophesize(CollectorFactory::class)->reveal()
        );

        $this->assertEquals($expected, $stat);
    }
}
