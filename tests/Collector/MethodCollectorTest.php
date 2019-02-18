<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Collector;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\Collector\Registry;
use SensioLabs\Deptrac\Collector\MethodCollector;

class MethodCollectorTest extends TestCase
{
    public function dataProviderSatisfy(): iterable
    {
        yield [
            ['name' => 'abc'],
            [
                $this->getClassMethod('abc'),
                $this->getClassMethod('abcdef'),
                $this->getClassMethod('xyz'),
            ],
            true,
        ];

        yield [
            ['name' => 'abc'],
            [
                $this->getClassMethod('abc'),
                $this->getClassMethod('xyz'),
            ],
            true,
        ];

        yield [
            ['name' => 'abc'],
            [
                $this->getClassMethod('xyz'),
            ],
            false,
        ];
    }

    public function testType(): void
    {
        static::assertEquals('method', (new MethodCollector())->getType());
    }

    private function getClassMethod(string $name): \stdClass
    {
        $classMethod = new \stdClass();
        $classMethod->name = $name;

        return $classMethod;
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, array $methods, bool $expected): void
    {
        $className = 'foo';

        $astClassReference = new AstMap\AstClassReference($className);

        $ast = $this->createMock(Node::class);

        $parser = $this->createMock(NikicPhpParser::class);
        $parser->method('getAstForClassname')->willReturn($ast);
        $parser
            ->method('findNodesOfType')
            ->with((array) $ast, ClassMethod::class)
            ->willReturn($methods);

        $stat = (new MethodCollector())->satisfy(
            $configuration,
            $astClassReference,
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class),
            $parser
        );

        static::assertEquals($expected, $stat);
    }
}
