<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstDependency;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\AstRunner\Resolver\ClassDependencyResolver;

class AstClassReferenceResolver extends NodeVisitorAbstract
{
    private $fileReference;

    /** @var AstClassReference */
    private $currentClassReference;

    /** @var iterable|ClassDependencyResolver[] */
    private $classDependencyResolvers;

    /**
     * @param iterable|ClassDependencyResolver[] $classDependencyResolvers
     */
    public function __construct(AstFileReference $fileReference, iterable $classDependencyResolvers)
    {
        $this->fileReference = $fileReference;
        $this->classDependencyResolvers = $classDependencyResolvers;
    }

    public function enterNode(Node $node)
    {
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

        $this->currentClassReference = $this->fileReference->addClassReference($className);

        if ($node instanceof Node\Stmt\Class_) {
            if ($node->extends instanceof Node\Name) {
                $this->currentClassReference->addInherit(
                    AstInherit::newExtends(
                        $node->extends->toString(),
                        new FileOccurrence($this->fileReference, $node->extends->getLine())
                    )
                );
            }
            foreach ($node->implements as $implement) {
                $this->currentClassReference->addInherit(
                    AstInherit::newImplements(
                        $implement->toString(),
                        new FileOccurrence($this->fileReference, $implement->getLine())
                    )
                );
            }
        }

        if ($node instanceof Node\Stmt\Interface_) {
            foreach ($node->extends as $extend) {
                $this->currentClassReference->addInherit(
                    AstInherit::newExtends(
                        $extend->toString(),
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
            $this->fileReference->addDependency(
                AstDependency::useStmt(
                    $node->name->toString(),
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
                        $trait->toString(),
                        new FileOccurrence($this->fileReference, $trait->getLine())
                    )
                );
            }
        }

        if ($node instanceof Node\Expr\Instanceof_ && $node->class instanceof Node\Name) {
            $this->currentClassReference->addDependency(
                AstDependency::instanceofExpr(
                    $node->class->toString(),
                    new FileOccurrence($this->fileReference, $node->class->getLine())
                )
            );
        }

        if ($node instanceof Node\Param && $node->type instanceof Node\Name) {
            $this->currentClassReference->addDependency(
                AstDependency::parameter(
                    $node->type->toString(),
                    new FileOccurrence($this->fileReference, $node->type->getLine())
                )
            );
        }

        if ($node instanceof Node\Expr\New_ && $node->class instanceof Node\Name) {
            $this->currentClassReference->addDependency(
                AstDependency::newStmt(
                    $node->class->toString(),
                    new FileOccurrence($this->fileReference, $node->class->getLine())
                )
            );
        }

        if ($node instanceof Node\Expr\StaticPropertyFetch && $node->class instanceof Node\Name) {
            $this->currentClassReference->addDependency(
                AstDependency::staticProperty(
                    $node->class->toString(),
                    new FileOccurrence($this->fileReference, $node->class->getLine())
                )
            );
        }

        if ($node instanceof Node\Expr\StaticCall && $node->class instanceof Node\Name) {
            $this->currentClassReference->addDependency(
                AstDependency::staticMethod(
                    $node->class->toString(),
                    new FileOccurrence($this->fileReference, $node->class->getLine())
                )
            );
        }

        if ($node instanceof Node\Stmt\ClassMethod || $node instanceof Node\Expr\Closure) {
            if ($node->returnType instanceof Node\Name) {
                $this->currentClassReference->addDependency(
                    AstDependency::returnType(
                        $node->returnType->toString(),
                        new FileOccurrence($this->fileReference, $node->returnType->getLine())
                    )
                );
            } elseif ($node->returnType instanceof Node\NullableType) {
                $this->currentClassReference->addDependency(
                    AstDependency::returnType(
                        (string) $node->returnType->type,
                        new FileOccurrence($this->fileReference, $node->returnType->getLine())
                    )
                );
            }
        }

        if ($node instanceof Node\Stmt\Catch_) {
            foreach ($node->types as $type) {
                $this->currentClassReference->addDependency(
                    AstDependency::catchStmt(
                        $type->toString(),
                        new FileOccurrence($this->fileReference, $type->getLine())
                    )
                );
            }
        }

        foreach ($this->classDependencyResolvers as $resolver) {
            $resolver->processNode($node, $this->fileReference, $this->currentClassReference);
        }

        return null;
    }
}
