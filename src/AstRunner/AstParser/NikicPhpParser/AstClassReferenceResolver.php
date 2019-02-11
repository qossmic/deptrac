<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class AstClassReferenceResolver extends NodeVisitorAbstract
{
    private $fileReference;

    /** @var AstClassReference */
    private $currentClassReference;

    public function __construct(AstFileReference $fileReference)
    {
        $this->fileReference = $fileReference;
    }

    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\ClassLike) {
            return;
        }

        if (isset($node->namespacedName) && $node->namespacedName instanceof Node\Name) {
            $className = $node->namespacedName->toString();
        } elseif ($node->name instanceof Node\Identifier) {
            $className = $node->name->toString();
        } else {
            return; // map anonymous classes on current class
        }

        $this->currentClassReference = $this->fileReference->addClassReference($className);
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\UseUse) {
            $this->fileReference->addUse($node->name->toString(), $node->name->getLine());
        }

        if (null === $this->currentClassReference) {
            return;
        }

        if ($node instanceof Node\Expr\Instanceof_ && $node->class instanceof Node\Name) {
            $this->currentClassReference->addInstanceof($node->class->toString(), $node->class->getLine());
        }

        if ($node instanceof Node\Param && $node->type instanceof Node\Name) {
            $this->currentClassReference->addParameter($node->type->toString(), $node->getLine());
        }

        if ($node instanceof Node\Expr\New_ && $node->class instanceof Node\Name) {
            $this->currentClassReference->addNewStmt($node->class->toString(), $node->class->getLine());
        }

        if ($node instanceof Node\Expr\StaticPropertyFetch && $node->class instanceof Node\Name) {
            $this->currentClassReference->addStaticPropertyAccess($node->class->toString(), $node->class->getLine());
        }

        if ($node instanceof Node\Expr\StaticCall && $node->class instanceof Node\Name) {
            $this->currentClassReference->addStaticMethodCall($node->class->toString(), $node->class->getLine());
        }

        if ($node instanceof Node\Stmt\ClassMethod || $node instanceof Node\Expr\Closure) {
            if ($node->returnType instanceof Node\Name) {
                $this->currentClassReference->addReturnType(
                    $node->returnType->toString(),
                    $node->returnType->getLine()
                );
            } elseif ($node->returnType instanceof Node\NullableType) {
                $this->currentClassReference->addReturnType(
                    (string) $node->returnType->type,
                    $node->returnType->getLine()
                );
            }
        }
    }
}
