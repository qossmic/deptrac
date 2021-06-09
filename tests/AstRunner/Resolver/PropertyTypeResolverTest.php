<?php

namespace Tests\Qossmic\Deptrac\AstRunner\Resolver;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstParser\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use Qossmic\Deptrac\AstRunner\Resolver\PropertyTypeResolver;
use Qossmic\Deptrac\AstRunner\Resolver\TypeResolver;
use Symfony\Component\Finder\SplFileInfo;

final class PropertyTypeResolverTest extends TestCase
{
    public function testPropertyDependencyResolving(): void
    {
        $typeResolver = new TypeResolver();
        $parser = new NikicPhpParser(
            ParserFactory::createParser(),
            new AstFileReferenceInMemoryCache(),
            new TypeResolver(),
            new PropertyTypeResolver($typeResolver)
        );

        $filePath = __DIR__.'/Fixtures/PropertyTypeDependency.php';
        $astFileReference = $parser->parseFile($filePath, null);

        $astClassReferences = $astFileReference->getAstClassReferences();
        self::assertCount(1, $astClassReferences);
        $propertyDependencies = $astClassReferences[0]->getDependencies();
        self::assertCount(4, $propertyDependencies);

        $this->assertSame(SplFileInfo::class, $propertyDependencies[0]->getClassLikeName()->toString());
        $this->assertSame(\SplFileInfo::class, $propertyDependencies[1]->getClassLikeName()->toString());
        $this->assertSame(\DateTimeInterface::class, $propertyDependencies[2]->getClassLikeName()->toString());
        $this->assertSame(SplFileInfo::class, $propertyDependencies[3]->getClassLikeName()->toString());
    }
}
