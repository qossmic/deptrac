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
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeScope;

class ClassReferenceVisitor extends NodeVisitorAbstract
{
    private $fileReference;

    /** @var ClassDependencyResolver[] */
    private $classDependencyResolvers;

    /** @var TypeScope */
    private $currentTypeScope;

    /** @var ClassReferenceBuilder */
    private $currentClassReferenceBuilder;

    /** @var TypeResolver */
    private $typeResolver;

    public function __construct(AstFileReference $fileReference, TypeResolver $typeResolver, ClassDependencyResolver ...$classDependencyResolvers)
    {
        $this->currentTypeScope = new TypeScope('');
        $this->fileReference = $fileReference;
        $this->classDependencyResolvers = $classDependencyResolvers;
        $this->typeResolver = $typeResolver;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->currentTypeScope = new TypeScope($node->name ? $node->name->toCodeString() : '');
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
            $this->currentTypeScope->addUse($node->name->toCodeString(), $node->getAlias()->toString());
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
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, ...$node->traits) as $classLikeName) {
                $this->currentClassReferenceBuilder->trait($classLikeName, $node->getLine());
            }
        }

        if ($node instanceof Node\Expr\Instanceof_ && $node->class instanceof Node\Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $this->currentClassReferenceBuilder->instanceof($classLikeName, $node->class->getLine());
            }
        }

        if ($node instanceof Node\Param && null !== $node->type) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->type) as $classLikeName) {
                $this->currentClassReferenceBuilder->parameter($classLikeName, $node->type->getLine());
            }
        }

        if ($node instanceof Node\Expr\New_ && $node->class instanceof Node\Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $this->currentClassReferenceBuilder->newStatement($classLikeName, $node->class->getLine());
            }
        }

        if ($node instanceof Node\Expr\StaticPropertyFetch && $node->class instanceof Node\Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $this->currentClassReferenceBuilder->staticProperty($classLikeName, $node->class->getLine());
            }
        }

        if ($node instanceof Node\Expr\StaticCall && $node->class instanceof Node\Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $this->currentClassReferenceBuilder->staticMethod($classLikeName, $node->class->getLine());
            }
        }

        if (($node instanceof Node\Stmt\ClassMethod || $node instanceof Node\Expr\Closure) && null !== $node->returnType) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->returnType) as $classLikeName) {
                $this->currentClassReferenceBuilder->returnType($classLikeName, $node->returnType->getLine());
            }
        }

        if ($node instanceof Node\Stmt\Catch_) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, ...$node->types) as $classLikeName) {
                $this->currentClassReferenceBuilder->catchStmt($classLikeName, $node->getLine());
            }
        }

        foreach ($this->classDependencyResolvers as $resolver) {
            $resolver->processNode($node, $this->currentClassReferenceBuilder, $this->currentTypeScope);
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
}
