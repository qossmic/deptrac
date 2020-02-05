<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
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
            $this->currentTypeContext = new NameScope($node->name ? $node->name->toCodeString() : 'global');
        }

        if (!$node instanceof Node\Stmt\ClassLike) {
            return null;
        }

        if (isset($node->namespacedName) && $node->namespacedName instanceof Node\Name) {
            $className = $node->namespacedName->toCodeString();
        } elseif ($node->name instanceof Node\Identifier) {
            $className = $node->name->toCodeString();
        } else {
            return null; // map anonymous classes on current class
        }

        if (null !== $this->currentClassReferenceBuilder) {
            $this->currentClassReferenceBuilder->build();
        }

        $this->currentClassReferenceBuilder = ClassReferenceBuilder::create($this->fileReference, $className);

        if ($node instanceof Node\Stmt\Class_) {
            if ($node->extends instanceof Node\Name) {
                $this->currentClassReferenceBuilder->extends($node->extends->toCodeString(), $node->extends->getLine());
            }
            foreach ($node->implements as $implement) {
                $this->currentClassReferenceBuilder->implements($implement->toCodeString(), $implement->getLine());
            }
        }

        if ($node instanceof Node\Stmt\Interface_) {
            foreach ($node->extends as $extend) {
                $this->currentClassReferenceBuilder->extends($extend->toCodeString(), $extend->getLine());
            }
        }

        return null;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\UseUse) {
            $this->currentTypeContext->addUse($node->name->toCodeString(), $node->getAlias()->toString());
            $this->fileReference->addDependency(
                AstDependency::useStmt(
                    ClassLikeName::fromFQCN($node->name->toCodeString()),
                    new FileOccurrence($this->fileReference, $node->name->getLine())
                )
            );
        }

        if (null === $this->currentClassReferenceBuilder) {
            return null;
        }

        if ($node instanceof Node\Stmt\TraitUse) {
            foreach ($node->traits as $trait) {
                $this->currentClassReferenceBuilder->trait($trait->toCodeString(), $trait->getLine());
            }
        }

        if ($node instanceof Node\Expr\Instanceof_ && $this->isQualifiedType($node->class)) {
            $this->currentClassReferenceBuilder->instanceof($node->class->toCodeString(), $node->class->getLine());
        }

        if ($node instanceof Node\Param && $this->isQualifiedType($node->type)) {
            $this->currentClassReferenceBuilder->parameter($node->type->toCodeString(), $node->type->getLine());
        }

        if ($node instanceof Node\Expr\New_ && $this->isQualifiedType($node->class)) {
            $this->currentClassReferenceBuilder->newStatement($node->class->toCodeString(), $node->class->getLine());
        }

        if ($node instanceof Node\Expr\StaticPropertyFetch && $this->isQualifiedType($node->class)) {
            $this->currentClassReferenceBuilder->staticProperty($node->class->toCodeString(), $node->class->getLine());
        }

        if ($node instanceof Node\Expr\StaticCall && $this->isQualifiedType($node->class)) {
            $this->currentClassReferenceBuilder->staticMethod($node->class->toCodeString(), $node->class->getLine());
        }

        if ($node instanceof Node\Stmt\ClassMethod || $node instanceof Node\Expr\Closure) {
            if ($this->isQualifiedType($node->returnType)) {
                $this->currentClassReferenceBuilder->returnType($node->returnType->toCodeString(), $node->returnType->getLine());
            } elseif ($node->returnType instanceof Node\NullableType && $this->isQualifiedType($node->returnType->type)) {
                $this->currentClassReferenceBuilder->returnType($node->returnType->type->toCodeString(), $node->returnType->getLine());
            }
        }

        if ($node instanceof Node\Stmt\Catch_) {
            foreach ($node->types as $type) {
                $this->currentClassReferenceBuilder->catchStmt($type->toCodeString(), $type->getLine());
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

    private function isQualifiedType($type): bool
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
