<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner\Resolver;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstParser\BetterReflection\Parser;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;
use SplFileInfo;

class AnonymousClassResolverTest extends TestCase
{
    public function testPropertyDependencyResolving(): void
    {
        $parser = new Parser(new TypeResolver());

        $filePath = __DIR__.'/Fixtures/AnonymousClass.php';
        $astFileReference = $parser->parse(new SplFileInfo($filePath));

        $astClassReferences = $astFileReference->getAstClassReferences();

        static::assertCount(3, $astClassReferences);
        static::assertCount(0, $astClassReferences[0]->getDependencies());
        static::assertCount(0, $astClassReferences[1]->getDependencies());
        static::assertCount(2, $astClassReferences[2]->getDependencies());

        $dependencies = $astClassReferences[2]->getDependencies();

        static::assertSame(
            'Tests\SensioLabs\Deptrac\AstRunner\Resolver\Fixtures\ClassA',
            $dependencies[0]->getClassLikeName()->toString()
        );
        static::assertSame($filePath, $dependencies[0]->getFileOccurrence()->getFilepath());
        static::assertSame(19, $dependencies[0]->getFileOccurrence()->getLine());
        static::assertSame('anonymous_class_extends', $dependencies[0]->getType());

        static::assertSame(
            'Tests\SensioLabs\Deptrac\AstRunner\Resolver\Fixtures\InterfaceC',
            $dependencies[1]->getClassLikeName()->toString()
        );
        static::assertSame($filePath, $dependencies[1]->getFileOccurrence()->getFilepath());
        static::assertSame(19, $dependencies[1]->getFileOccurrence()->getLine());
        static::assertSame('anonymous_class_implements', $dependencies[1]->getType());
    }
}
