<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\AstRunner\Resolver;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstParser\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\ParserFactory;
use Qossmic\Deptrac\AstRunner\Resolver\ClassConstantResolver;
use Qossmic\Deptrac\AstRunner\Resolver\ClassMethodResolver;
use Qossmic\Deptrac\AstRunner\Resolver\TypeResolver;
use Qossmic\Deptrac\Configuration\ConfigurationAnalyser;
use Roave\BetterReflection\BetterReflection;

final class ClassMethodResolverTest extends TestCase
{
    public function violationClassesDataProvider(): array
    {
        return [
            'Contains violation in a variable' => [
                __DIR__ . '/../Fixtures/MethodCall/ContainsViolationWithVariable.php'
            ],
            'Contains violation without a variable' => [
                __DIR__ . '/../Fixtures/MethodCall/ContainsViolationWithoutVariable.php'
            ],
        ];
    }
    /**
     * @dataProvider violationClassesDataProvider
     */
    public function testPropertyDependencyResolving(string $filePath): void
    {
        $parser = new NikicPhpParser(
            ParserFactory::createParser(),
            new AstFileReferenceInMemoryCache(),
            new TypeResolver(),
            new ClassConstantResolver(),
            new ClassMethodResolver(new TypeResolver(), new BetterReflection())
        );
        
        $astFileReference = $parser->parseFile($filePath, ConfigurationAnalyser::fromArray([]));

        $astClassReferences = $astFileReference->getAstClassReferences();

        self::assertCount(1, $astClassReferences);


        $dependencies = $astClassReferences[0]->getDependencies();
        self::assertCount(2, $dependencies);
        self::assertSame(
            'Tests\Qossmic\Deptrac\AstRunner\Fixtures\MethodCall\DummyClassA',
            $dependencies[0]->getTokenName()->toString()
        );
        self::assertSame(
            'Tests\Qossmic\Deptrac\AstRunner\Fixtures\MethodCall\DummyViolationClass',
            $dependencies[1]->getTokenName()->toString()
        );
    }
}
