<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstDependency;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\AstRunner\Resolver\ClassDependencyResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\NameScope;

class AstClassReferenceResolver extends NodeVisitorAbstract
{
    private $fileReference;

    /** @var ClassDependencyResolver[] */
    private $classDependencyResolvers;

    /** @var NameScope */
    private $currentTypeContext;

    /** @var ClassReferenceBuilder */
    private $currentClassReferenceBuilder;

    public function __construct(AstFileReference $fileReference, ClassDependencyResolver ...$classDependencyResolvers)
    {
        $this->currentTypeContext = new NameScope('global');
        $this->fileReference = $fileReference;
        $this->classDependencyResolvers = $classDependencyResolvers;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->currentTypeContext = new NameScope($node->name ? $node->name->toString() : 'global');
        }

        if (!$node instanceof Node\Stmt\ClassLike) {
            return null;
        }

        if (isset($node->namespacedName) && $node->namespacedName instanceof Node\Name) {
            $className = $node->namespacedName->toString();
        } elseif ($node->name instanceof Node\Identifier) {
            $className = $node->name->toString();
        } else {
            return null; // map anonymous classes on current class
        }

        if (null !== $this->currentClassReferenceBuilder) {
            $this->currentClassReferenceBuilder->build();
        }

        $this->currentClassReferenceBuilder = ClassReferenceBuilder::create($this->fileReference, $className);

        if ($node instanceof Node\Stmt\Class_) {
            if ($node->extends instanceof Node\Name) {
                $this->currentClassReferenceBuilder->extends($node->extends->toString(), $node->extends->getLine());
            }
            foreach ($node->implements as $implement) {
                $this->currentClassReferenceBuilder->implements($implement->toString(), $implement->getLine());
            }
        }

        if ($node instanceof Node\Stmt\Interface_) {
            foreach ($node->extends as $extend) {
                $this->currentClassReferenceBuilder->extends($extend->toString(), $extend->getLine());
            }
        }

        return null;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\UseUse) {
            $this->currentTypeContext->addUse($node->name->toString(), $node->getAlias()->toString());
            $this->fileReference->addDependency(
                AstDependency::useStmt(
                    ClassLikeName::fromString($node->name->toString()),
                    new FileOccurrence($this->fileReference, $node->name->getLine())
                )
            );
        }

        if (null === $this->currentClassReferenceBuilder) {
            return null;
        }

        if ($node instanceof Node\Stmt\TraitUse) {
            foreach ($node->traits as $trait) {
                $this->currentClassReferenceBuilder->trait($trait->toString(), $trait->getLine());
            }
        }

        if ($node instanceof Node\Expr\Instanceof_ && $this->isQualifiedClassName($node->class)) {
            $this->currentClassReferenceBuilder->instanceof($node->class->toString(), $node->class->getLine());
        }

        if ($node instanceof Node\Param && $this->isQualifiedClassName($node->type)) {
            $this->currentClassReferenceBuilder->parameter($node->type->toString(), $node->type->getLine());
        }

        if ($node instanceof Node\Expr\New_ && $this->isQualifiedClassName($node->class)) {
            $this->currentClassReferenceBuilder->newStatement($node->class->toString(), $node->class->getLine());
        }

        if ($node instanceof Node\Expr\StaticPropertyFetch && $this->isQualifiedClassName($node->class)) {
            $this->currentClassReferenceBuilder->staticProperty($node->class->toString(), $node->class->getLine());
        }

        if ($node instanceof Node\Expr\StaticCall && $this->isQualifiedClassName($node->class)) {
            $this->currentClassReferenceBuilder->staticMethod($node->class->toString(), $node->class->getLine());
        }

        if ($node instanceof Node\Stmt\ClassMethod || $node instanceof Node\Expr\Closure) {
            if ($this->isQualifiedClassName($node->returnType)) {
                $this->currentClassReferenceBuilder->returnType($node->returnType->toString(), $node->returnType->getLine());
            } elseif ($node->returnType instanceof Node\NullableType && $this->isQualifiedClassName($node->returnType->type)) {
                $this->currentClassReferenceBuilder->returnType($node->returnType->type->toString(), $node->returnType->getLine());
            }
        }

        if ($node instanceof Node\Stmt\Catch_) {
            foreach ($node->types as $type) {
                $this->currentClassReferenceBuilder->catchStmt($type->toString(), $type->getLine());
            }
        }

        foreach ($this->classDependencyResolvers as $resolver) {
            $resolver->processNode($node, $this->currentClassReferenceBuilder, $this->currentTypeContext);
        }

        return null;
    }

    public function afterTraverse(array $nodes)
    {
        if (null !== $this->currentClassReferenceBuilder) {
            $this->currentClassReferenceBuilder->build();
        }

        return null;
    }

    private function isQualifiedClassName($type): bool
    {
        if (null === $type) {
            return false;
        }

        if ($type instanceof Node\Name) {
            return !$type->isSpecialClassName();
        }

        return false;
    }
}
