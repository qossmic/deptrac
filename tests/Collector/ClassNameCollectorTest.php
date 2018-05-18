<?php

namespace Tests\SensioLabs\Deptrac\Collector;

use PHPUnit\Framework\TestCase;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\Collector\ClassNameCollector;
use SensioLabs\Deptrac\Collector\Registry;

class ClassNameCollectorTest extends TestCase
{
    public function dataProviderStatisfy()
    {
        yield [['regex' => 'a'], 'foo\bar', true];
        yield [['regex' => 'a'], 'foo\bbr', false];
    }

    public function testType()
    {
        $this->assertEquals('className', (new ClassNameCollector())->getType());
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
            $this->prophesize(AstParserInterface::class)->reveal()
        );

        $this->assertEquals($expected, $stat);
    }

    /**
     * @expectedException \LogicException
     */
    public function testWrongRegexParam()
    {
        (new ClassNameCollector())->satisfy(
            ['Foo' => 'a'],
            $this->prophesize(AstClassReferenceInterface::class)->reveal(),
            $this->prophesize(AstMap::class)->reveal(),
            $this->prophesize(AstParserInterface::class)->reveal()
        );
    }
}
