<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Roave\BetterReflection\TypesFinder\PhpDocumentor\NamespaceNodeToReflectionTypeContext;
use SensioLabs\Deptrac\AstRunner\AstMap\FileReferenceBuilder;
use SensioLabs\Deptrac\AstRunner\Resolver\ClassDependencyResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;

class ClassReferenceVisitor extends NodeVisitorAbstract
{
    /** @var FileReferenceBuilder */
    private $fileReferenceBuilder;

    /** @var ClassDependencyResolver[] */
    private $classDependencyResolvers;

    /** @var Context */
    private $currentContext;

    /** @var TypeResolver */
    private $typeResolver;

    private $namespaceNodeToReflectionTypeContext;

    public function __construct(FileReferenceBuilder $fileReferenceBuilder, TypeResolver $typeResolver, ClassDependencyResolver ...$classDependencyResolvers)
    {
        $this->currentContext = new Context('');
        $this->fileReferenceBuilder = $fileReferenceBuilder;
        $this->classDependencyResolvers = $classDependencyResolvers;
        $this->typeResolver = $typeResolver;
        $this->namespaceNodeToReflectionTypeContext = new NamespaceNodeToReflectionTypeContext();
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->currentContext = ($this->namespaceNodeToReflectionTypeContext)($node);
        }

        if (!$node instanceof Node\Stmt\ClassLike) {
            return null;
        }

        if (isset($node->namespacedName) && $node->namespacedName instanceof Node\Name) {
            $className = $node->namespacedName->toCodeString();
        } elseif ($node->name instanceof Node\Identifier) {
            $className = $node->name->toString();
        } else {
            return null; // map anonymous classes on current class
        }

        $classReferenceBuilder = $this->fileReferenceBuilder->newClassLike($className);

        if ($node instanceof Node\Stmt\Class_) {
            if ($node->extends instanceof Node\Name) {
                $classReferenceBuilder->extends($node->extends->toCodeString(), $node->extends->getLine());
            }
            foreach ($node->implements as $implement) {
                $classReferenceBuilder->implements($implement->toCodeString(), $implement->getLine());
            }
        }

        if ($node instanceof Node\Stmt\Interface_) {
            foreach ($node->extends as $extend) {
                $classReferenceBuilder->implements($extend->toCodeString(), $extend->getLine());
            }
        }

        return null;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\UseUse) {
            $this->fileReferenceBuilder->use($node->name->toCodeString(), $node->name->getLine());
        }

        if (!$this->fileReferenceBuilder->hasCurrentClassLike()) {
            return null;
        }

        $classReferenceBuilder = $this->fileReferenceBuilder->currentClassLike();

        if ($node instanceof Node\Stmt\TraitUse) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentContext, ...$node->traits) as $classLikeName) {
                $classReferenceBuilder->trait($classLikeName, $node->getLine());
            }
        }

        if ($node instanceof Node\Param && null !== $node->type) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentContext, $node->type) as $classLikeName) {
                $classReferenceBuilder->parameter($classLikeName, $node->type->getLine());
            }
        }

        if (($node instanceof Node\Stmt\ClassMethod || $node instanceof Node\Expr\Closure) && null !== $node->returnType) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentContext, $node->returnType) as $classLikeName) {
                $classReferenceBuilder->returnType($classLikeName, $node->returnType->getLine());
            }
        }

        foreach ($this->classDependencyResolvers as $resolver) {
            $resolver->processNode($node, $classReferenceBuilder, $this->currentContext);
        }

        return null;
    }

    public function afterTraverse(array $nodes)
    {
        return null;
    }
}
