<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstDependency;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\AstRunner\Resolver\ClassDependencyResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\NameScope;

class AstClassReferenceResolver extends NodeVisitorAbstract
{
    private $fileReference;

    /** @var AstClassReference */
    private $currentClassReference;

    /** @var ClassDependencyResolver[] */
    private $classDependencyResolvers;

    /** @var Context */
    private $currentTypeContext;

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
            $className = ClassLikeName::fromString($node->namespacedName->toString());
        } elseif ($node->name instanceof Node\Identifier) {
            $className = ClassLikeName::fromString($node->name->toString());
        } else {
            return null; // map anonymous classes on current class
        }

        $this->currentClassReference = $this->fileReference->addClassReference($className);

        if ($node instanceof Node\Stmt\Class_) {
            if ($node->extends instanceof Node\Name) {
                $this->currentClassReference->addInherit(
                    AstInherit::newExtends(
                        ClassLikeName::fromString($node->extends->toString()),
                        new FileOccurrence($this->fileReference, $node->extends->getLine())
                    )
                );
            }
            foreach ($node->implements as $implement) {
                $this->currentClassReference->addInherit(
                    AstInherit::newImplements(
                        ClassLikeName::fromString($implement->toString()),
                        new FileOccurrence($this->fileReference, $implement->getLine())
                    )
                );
            }
        }

        if ($node instanceof Node\Stmt\Interface_) {
            foreach ($node->extends as $extend) {
                $this->currentClassReference->addInherit(
                    AstInherit::newExtends(
                        ClassLikeName::fromString($extend->toString()),
                        new FileOccurrence($this->fileReference, $extend->getLine())
                    )
                );
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

        if (null === $this->currentClassReference) {
            return null;
        }

        if ($node instanceof Node\Stmt\TraitUse) {
            foreach ($node->traits as $trait) {
                $this->currentClassReference->addInherit(
                    AstInherit::newTraitUse(
                        ClassLikeName::fromString($trait->toString()),
                        new FileOccurrence($this->fileReference, $trait->getLine())
                    )
                );
            }
        }

        if ($node instanceof Node\Expr\Instanceof_ && $this->isQualifiedClassName($node->class)) {
            $this->currentClassReference->addDependency(
                AstDependency::instanceofExpr(
                    ClassLikeName::fromString($node->class->toString()),
                    new FileOccurrence($this->fileReference, $node->class->getLine())
                )
            );
        }

        if ($node instanceof Node\Param && $this->isQualifiedClassName($node->type)) {
            $this->currentClassReference->addDependency(
                AstDependency::parameter(
                    ClassLikeName::fromString($node->type->toString()),
                    new FileOccurrence($this->fileReference, $node->type->getLine())
                )
            );
        }

        if ($node instanceof Node\Expr\New_ && $this->isQualifiedClassName($node->class)) {
            $this->currentClassReference->addDependency(
                AstDependency::newStmt(
                    ClassLikeName::fromString($node->class->toString()),
                    new FileOccurrence($this->fileReference, $node->class->getLine())
                )
            );
        }

        if ($node instanceof Node\Expr\StaticPropertyFetch && $this->isQualifiedClassName($node->class)) {
            $this->currentClassReference->addDependency(
                AstDependency::staticProperty(
                    ClassLikeName::fromString($node->class->toString()),
                    new FileOccurrence($this->fileReference, $node->class->getLine())
                )
            );
        }

        if ($node instanceof Node\Expr\StaticCall && $this->isQualifiedClassName($node->class)) {
            $this->currentClassReference->addDependency(
                AstDependency::staticMethod(
                    ClassLikeName::fromString($node->class->toString()),
                    new FileOccurrence($this->fileReference, $node->class->getLine())
                )
            );
        }

        if ($node instanceof Node\Stmt\ClassMethod || $node instanceof Node\Expr\Closure) {
            if ($this->isQualifiedClassName($node->returnType)) {
                $this->currentClassReference->addDependency(
                    AstDependency::returnType(
                        ClassLikeName::fromString($node->returnType->toString()),
                        new FileOccurrence($this->fileReference, $node->returnType->getLine())
                    )
                );
            } elseif ($node->returnType instanceof Node\NullableType && $this->isQualifiedClassName($node->returnType->type)) {
                $this->currentClassReference->addDependency(
                    AstDependency::returnType(
                        ClassLikeName::fromString((string) $node->returnType->type),
                        new FileOccurrence($this->fileReference, $node->returnType->getLine())
                    )
                );
            }
        }

        if ($node instanceof Node\Stmt\Catch_) {
            foreach ($node->types as $type) {
                if (!$this->isQualifiedClassName($type)) {
                    continue;
                }

                $this->currentClassReference->addDependency(
                    AstDependency::catchStmt(
                        ClassLikeName::fromString($type->toString()),
                        new FileOccurrence($this->fileReference, $type->getLine())
                    )
                );
            }
        }

        foreach ($this->classDependencyResolvers as $resolver) {
            $resolver->processNode($node, $this->fileReference, $this->currentClassReference, $this->currentTypeContext);
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
