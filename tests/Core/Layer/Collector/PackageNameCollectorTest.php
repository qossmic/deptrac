<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Layer\Collector;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Layer\InvalidCollectorDefinitionException;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Core\Layer\Collector\PackageNameCollector;

final class PackageNameCollectorTest extends TestCase
{
    private NikicPhpParser $astParser;
    private PackageNameCollector $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->astParser = $this->createMock(NikicPhpParser::class);

        $this->collector = new PackageNameCollector($this->astParser);
    }

    public function provideSatisfy(): iterable
    {
        yield [
            ['value' => 'abc'],
            $this->getPackageDocBlock(['abc', 'abcdef', 'xyz']),
            true,
        ];

        yield [
            ['value' => 'abc'],
            $this->getPackageDocBlock(['abc', 'xyz']),
            true,
        ];

        yield [
            ['value' => 'abc'],
            $this->getPackageDocBlock(['xyz']),
            false,
        ];
    }

    /**
     * @dataProvider provideSatisfy
     */
    public function testSatisfy(array $configuration, Doc $docBlock, bool $expected): void
    {
        $astClassReference = new ClassLikeReference(ClassLikeToken::fromFQCN('foo'));

        $classLike = $this->createMock(Node\Stmt\ClassLike::class);
        $classLike->method('getDocComment')->willReturn($docBlock);

        $this->astParser
            ->method('getNodeForClassLikeReference')
            ->with($astClassReference)
            ->willReturn($classLike);

        $actual = $this->collector->satisfy(
            $configuration,
            $astClassReference,
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
        );

        self::assertFalse($actual);
    }

    public function testMissingValueThrowsException(): void
    {
        $astClassReference = new ClassLikeReference(ClassLikeToken::fromFQCN('foo'));

        $this->expectException(InvalidCollectorDefinitionException::class);
        $this->expectExceptionMessage('PackageNameCollector needs the value configuration.');

        $this->collector->satisfy(
            [],
            $astClassReference,
        );
    }

    public function testInvalidRegexParam(): void
    {
        $astClassReference = new ClassLikeReference(ClassLikeToken::fromFQCN('foo'));

        $this->expectException(InvalidCollectorDefinitionException::class);

        $this->collector->satisfy(
            ['value' => '/'],
            $astClassReference,
        );
    }

    private function getPackageDocBlock(array $packageNames): Doc
    {
        return new Doc(sprintf(
            "    /**\n%s     */",
            implode('', array_map(static fn ($packageName) => '     * @package '.$packageName."\n", $packageNames))
        ));
    }
}
