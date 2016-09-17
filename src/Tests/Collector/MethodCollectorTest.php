<?php


namespace SensioLabs\Deptrac\Tests\Collector;


use PhpParser\Node\Stmt\ClassMethod;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\Collector\MethodCollector;
use SensioLabs\Deptrac\CollectorFactory;

class MethodCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function dataProviderStatisfy()
    {
        yield [
            ['name' => 'abc'],
            [
                $this->getClassMethod('abc'),
                $this->getClassMethod('abcdef'),
                $this->getClassMethod('xyz')
            ],
            true
       ];

        yield [
            ['name' => 'abc'],
            [
                $this->getClassMethod('abc'),
                $this->getClassMethod('xyz')
            ],
            true
        ];

        yield [
            ['name' => 'abc'],
            [
                $this->getClassMethod('xyz')
            ],
            false
        ];
    }

    public function testType()
    {
        $this->assertEquals('method', (new MethodCollector())->getType());
    }

    private function getClassMethod($name)
    {
        $classMethod = new \stdClass();
        $classMethod->name = $name;
        return $classMethod;
    }

    /**
     * @dataProvider dataProviderStatisfy
     */
    public function testStatisfy($configuration, $methods, $expected)
    {
        $className = "foo";

        $astClassReference = $this->prophesize(AstClassReferenceInterface::class);
        $astClassReference->getClassName()->willReturn($className);

        $parser = $this->prophesize(NikicPhpParser::class);
        $parser->getAstForClassname($className)->willReturn($ast = new \stdClass());


        $parser->findNodesOfType($ast, ClassMethod::class)->willReturn($methods);

        $stat = (new MethodCollector())->satisfy(
            $configuration,
            $astClassReference->reveal(),
            $this->prophesize(AstMap::class)->reveal(),
            $this->prophesize(CollectorFactory::class)->reveal(),
            $parser->reveal()
        );

        $this->assertEquals($expected, $stat);
    }
}
