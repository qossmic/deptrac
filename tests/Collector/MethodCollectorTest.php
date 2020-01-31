<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Collector;

use PhpParser\Node;
use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\Collector\MethodCollector;
use SensioLabs\Deptrac\Collector\Registry;

class MethodCollectorTest extends TestCase
{
    public function testType(): void
    {
        static::assertSame('method', (new MethodCollector($this->createMock(NikicPhpParser::class)))->getType());
    }

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

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, array $methods, bool $expected): void
    {
        $astClassReference = new AstClassReference(ClassLikeName::fromString('foo'));

        $classLike = $this->createMock(Node\Stmt\ClassLike::class);
        $classLike->method('getMethods')->willReturn($methods);

        $parser = $this->createMock(NikicPhpParser::class);
        $parser
            ->method('getAstForClassReference')
            ->with($astClassReference)
            ->willReturn($classLike);

        $stat = (new MethodCollector($parser))->satisfy(
            $configuration,
            $astClassReference,
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );

        static::assertSame($expected, $stat);
    }

    public function testClassLikeAstNotFoundDoesNotSatisfy(): void
    {
        $astClassReference = new AstClassReference(ClassLikeName::fromString('foo'));
        $parser = $this->createMock(NikicPhpParser::class);
        $parser
            ->method('getAstForClassReference')
            ->with($astClassReference)
            ->willReturn(null);

        $satisfy = (new MethodCollector($parser))->satisfy(
            ['name' => 'abc'],
            $astClassReference,
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );

        static::assertFalse($satisfy);
    }

    public function testMissingNameThrowsException(): void
    {
        $astClassReference = new AstClassReference(ClassLikeName::fromString('foo'));
        $parser = $this->createMock(NikicPhpParser::class);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('MethodCollector needs the name configuration.');

        (new MethodCollector($parser))->satisfy(
            [],
            $astClassReference,
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );
    }

    private function getClassMethod(string $name): \stdClass
    {
        $classMethod = new \stdClass();
        $classMethod->name = $name;

        return $classMethod;
    }
}
