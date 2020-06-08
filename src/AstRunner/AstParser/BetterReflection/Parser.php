<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\BetterReflection;

use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionParameter;
use Roave\BetterReflection\Reflection\ReflectionProperty;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\SourceLocator\Ast\Locator;
use Roave\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;
use Roave\BetterReflection\TypesFinder\PhpDocumentor\NamespaceNodeToReflectionTypeContext;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\FileReferenceBuilder;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParser;
use SensioLabs\Deptrac\AstRunner\PhpdocParser\ResolveNodeDocCommentTypes;
use SensioLabs\Deptrac\AstRunner\PhpParser\ResolveClassMethodDependencyAwareNodeTypes;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;
use SplFileInfo;

class Parser implements AstParser
{
    private Locator $locator;
    private NamespaceNodeToReflectionTypeContext $namespaceNodeToReflectionTypeContext;
    private ResolveNodeDocCommentTypes $resolveNodeDocCommentTypes;
    private ResolveClassMethodDependencyAwareNodeTypes $resolveClassMethodDependencyAwareNodeTypes;

    public function __construct(TypeResolver $typeResolver)
    {
        $this->locator = (new BetterReflection())->astLocator();

        $this->namespaceNodeToReflectionTypeContext = new NamespaceNodeToReflectionTypeContext();
        $this->resolveNodeDocCommentTypes = new ResolveNodeDocCommentTypes();
        $this->resolveClassMethodDependencyAwareNodeTypes = new ResolveClassMethodDependencyAwareNodeTypes($typeResolver);
    }

    public function parse(SplFileInfo $data): AstFileReference
    {
        $realPath = $data->getRealPath();
        $reflector = new ClassReflector(new SingleFileSourceLocator($realPath, $this->locator));

        $fileReferenceBuilder = FileReferenceBuilder::create($realPath);

        foreach ($reflector->getAllClasses() as $reflectionClass) {
            if ($reflectionClass->isAnonymous()) {
                continue;
            }

            $context = ($this->namespaceNodeToReflectionTypeContext)($reflectionClass->getDeclaringNamespaceAst());

            $classReferenceBuilder = $fileReferenceBuilder->newClassLike($reflectionClass->getName());

            if (null !== $parentClass = $reflectionClass->getParentClass()) {
                $classReferenceBuilder->extends($parentClass->getName(), $reflectionClass->getStartLine());
            }

            /** @var ReflectionClass $interface */
            foreach ($reflectionClass->getImmediateInterfaces() as $interface) {
                $classReferenceBuilder->implements($interface->getName(), $reflectionClass->getStartLine());
            }

            /** @var ReflectionClass $trait */
            foreach ($reflectionClass->getTraits() as $trait) {
                $classReferenceBuilder->trait($trait->getName(), $trait->getAst()->getStartLine());
            }

            /** @var ReflectionProperty $reflectionProperty */
            foreach ($reflectionClass->getImmediateProperties() as $reflectionProperty) {
                ($this->resolveNodeDocCommentTypes)($classReferenceBuilder, $reflectionProperty->getAst(), $context);

                if (null !== ($type = $reflectionProperty->getType()) && !$type->isBuiltin()) {
                    $classReferenceBuilder->property($type->getName(), $reflectionProperty->getStartLine());
                }
            }

            foreach ($reflectionClass->getImmediateMethods() as $reflectionMethod) {
                ($this->resolveNodeDocCommentTypes)($classReferenceBuilder, $reflectionMethod->getAst(), $context);

                /** @var ReflectionParameter $reflectionParameter */
                foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                    if (null !== ($type = $reflectionParameter->getType()) && !$type->isBuiltin()) {
                        $classReferenceBuilder->parameter($type->getName(), $reflectionMethod->getStartLine());
                    }
                }

                if (null !== ($returnType = $reflectionMethod->getReturnType()) && !$returnType->isBuiltin()) {
                    $classReferenceBuilder->returnType($returnType->getName(), $reflectionMethod->getStartLine());
                }

                ($this->resolveClassMethodDependencyAwareNodeTypes)($classReferenceBuilder, $reflectionMethod->getAst(), $context);
            }
        }

        return $fileReferenceBuilder->build();
    }
}
