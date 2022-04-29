<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Layer\Collector;

use LogicException;
use PhpParser\Node;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Layer\Collector\MethodCollector;
use stdClass;

final class MethodCollectorTest extends TestCase
{
    private NikicPhpParser $astParser;
    private MethodCollector $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->astParser = $this->createMock(NikicPhpParser::class);

        $this->collector = new MethodCollector($this->astParser);
    }

    public function provideSatisfy(): iterable
    {
        yield [
            ['value' => 'abc'],
            [
                $this->getClassMethod('abc'),
                $this->getClassMethod('abcdef'),
                $this->getClassMethod('xyz'),
            ],
            true,
        ];

        yield [
            ['value' => 'abc'],
            [
                $this->getClassMethod('abc'),
                $this->getClassMethod('xyz'),
            ],
            true,
        ];

        yield [
            ['value' => 'abc'],
            [
                $this->getClassMethod('xyz'),
            ],
            false,
        ];
    }

    /**
     * @dataProvider provideSatisfy
     */
    public function testSatisfy(array $configuration, array $methods, bool $expected): void
    {
        $astClassReference = new ClassLikeReference(ClassLikeToken::fromFQCN('foo'));

        $classLike = $this->createMock(Node\Stmt\ClassLike::class);
        $classLike->method('getMethods')->willReturn($methods);

        $this->astParser
            ->method('getNodeForClassLikeReference')
            ->with($astClassReference)
            ->willReturn($classLike);

        $actual = $this->collector->satisfy(
            $configuration,
            $astClassReference,
            new AstMap([])
        );

        self::assertSame($expected, $actual);
    }

    public function testClassLikeAstNotFoundDoesNotSatisfy(): void
    {
        $astClassReference = new ClassLikeReference(ClassLikeToken::fromFQCN('foo'));
        $this->astParser
            ->method('getNodeForClassLikeReference')
            ->with($astClassReference)
            ->willReturn(null);

        $actual = $this->collector->satisfy(
            ['value' => 'abc'],
            $astClassReference,
            new AstMap([])
        );

        self::assertFalse($actual);
    }

    public function testMissingNameThrowsException(): void
    {
        $astClassReference = new ClassLikeReference(ClassLikeToken::fromFQCN('foo'));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('MethodCollector needs the name configuration.');

        $this->collector->satisfy(
            [],
            $astClassReference,
            new AstMap([])
        );
    }

    public function testInvalidRegexParam(): void
    {
        $astClassReference = new ClassLikeReference(ClassLikeToken::fromFQCN('foo'));

        $this->expectException(LogicException::class);

        $this->collector->satisfy(
            ['value' => '/'],
            $astClassReference,
            new AstMap([])
        );
    }

    private function getClassMethod(string $name): stdClass
    {
        $classMethod = new stdClass();
        $classMethod->name = $name;

        return $classMethod;
    }
}
