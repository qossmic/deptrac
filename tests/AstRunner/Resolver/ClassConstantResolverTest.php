<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\AstRunner\Resolver;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstParser\BetterReflection\Parser;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;
use SplFileInfo;

class ClassConstantResolverTest extends TestCase
{
    public function testPropertyDependencyResolving(): void
    {
        $parser = new Parser(new TypeResolver());

        $filePath = __DIR__.'/Fixtures/ClassConst.php';
        $astFileReference = $parser->parse(new SplFileInfo($filePath));

        $astClassReferences = $astFileReference->getAstClassReferences();

        static::assertCount(2, $astClassReferences);
        static::assertCount(0, $astClassReferences[0]->getDependencies());
        static::assertCount(1, $astClassReferences[1]->getDependencies());

        $dependencies = $astClassReferences[1]->getDependencies();
        static::assertSame(
            'Tests\SensioLabs\Deptrac\AstRunner\Resolver\Fixtures\ClassA',
            $dependencies[0]->getClassLikeName()->toString()
        );
        static::assertSame($filePath, $dependencies[0]->getFileOccurrence()->getFilepath());
        static::assertSame(15, $dependencies[0]->getFileOccurrence()->getLine());
        static::assertSame('const', $dependencies[0]->getType());
    }
}
