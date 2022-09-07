<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Dependency;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileToken;
use Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike\FunctionLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike\FunctionLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\SuperGlobalToken;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\VariableReference;
use Qossmic\Deptrac\Core\Dependency\TokenResolver;

final class TokenResolverTest extends TestCase
{
    private TokenResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new TokenResolver();
    }

    public function testResolvesClassLikeNotInAstMap(): void
    {
        $astMap = new AstMap([]);
        $token = ClassLikeToken::fromFQCN('App\\Foo');

        $resolved = $this->resolver->resolve($token, $astMap);

        self::assertInstanceOf(ClassLikeReference::class, $resolved);
        self::assertSame($token->toString(), $resolved->getToken()->toString());
    }

    public function testResolvesClassLikeFromAstMap(): void
    {
        $token = ClassLikeToken::fromFQCN('App\\Foo');
        $classReference = new ClassLikeReference($token);
        $fileReference = new FileReference(
            'path/to/file.php',
            [
                $classReference,
            ],
            [],
            []
        );
        $astMap = new AstMap([$fileReference]);

        $resolved = $this->resolver->resolve($token, $astMap);

        self::assertInstanceOf(ClassLikeReference::class, $resolved);
        self::assertSame($token->toString(), $resolved->getToken()->toString());
    }

    public function testResolvesFunctionLikeNotInAstMap(): void
    {
        $astMap = new AstMap([]);
        $token = FunctionLikeToken::fromFQCN('App\\Foo::foo');

        $resolved = $this->resolver->resolve($token, $astMap);

        self::assertInstanceOf(FunctionLikeReference::class, $resolved);
        self::assertSame($token->toString(), $resolved->getToken()->toString());
    }

    public function testResolvesFunctionLikeFromAstMap(): void
    {
        $token = FunctionLikeToken::fromFQCN('App\\Foo::foo');
        $functionReference = new FunctionLikeReference($token);
        $fileReference = new FileReference(
            'path/to/file.php',
            [],
            [
                $functionReference,
            ],
            []
        );
        $astMap = new AstMap([$fileReference]);

        $resolved = $this->resolver->resolve($token, $astMap);

        self::assertInstanceOf(FunctionLikeReference::class, $resolved);
        self::assertSame($token->toString(), $resolved->getToken()->toString());
    }

    public function testResolvesSuperglobal(): void
    {
        $astMap = new AstMap([]);
        $token = SuperGlobalToken::from('_POST');

        $resolved = $this->resolver->resolve($token, $astMap);

        self::assertInstanceOf(VariableReference::class, $resolved);
        self::assertSame($token->toString(), $resolved->getToken()->toString());
    }

    public function testResolvesFileNotInAstMap(): void
    {
        $astMap = new AstMap([]);
        $token = new FileToken('path/to/file.php');

        $resolved = $this->resolver->resolve($token, $astMap);

        self::assertInstanceOf(FileReference::class, $resolved);
        self::assertSame($token->toString(), $resolved->getToken()->toString());
    }

    public function testResolvesFileFromAstMap(): void
    {
        $fileReference = new FileReference(
            'path/to/file.php',
            [],
            [],
            []
        );
        $astMap = new AstMap([$fileReference]);
        $token = new FileToken('path/to/file.php');

        $resolved = $this->resolver->resolve($token, $astMap);

        self::assertInstanceOf(FileReference::class, $resolved);
        self::assertSame($token->toString(), $resolved->getToken()->toString());
    }
}
