<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use Qossmic\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;

class ClassMethodResolver implements ClassDependencyResolver
{
    private $typeResolver;

    public function __construct(TypeResolver $typeResolver)
    {
        $this->typeResolver = $typeResolver;
    }

    public function processNode(Node $node, ClassReferenceBuilder $classReferenceBuilder, TypeScope $typeScope): void
    {
        if (!$node instanceof Node\Stmt\ClassMethod) {
            return;
        }

        $typeScope = $typeScope->enterClassMethod();

        if (null !== $node->returnType) {
            foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, $node->returnType) as $classLikeName) {
                $classReferenceBuilder->returnType($classLikeName, $node->returnType->getLine());
            }
        }

        if (null === $node->stmts) {
            return;
        }

        foreach ($node->stmts as $stmt) {
            if (!$stmt instanceof Node\Stmt\Expression) {
                continue;
            }

            if ($stmt->expr instanceof Node\Expr\Assign && $stmt->expr->var instanceof Node\Expr\Variable) {
                $assignExpr = $stmt->expr;
                /** @var Node\Expr\Variable $var */
                $var = $assignExpr->var;
                $assignExprExpr = $assignExpr->expr;

                if ($assignExprExpr instanceof Node\Expr\New_ && $assignExprExpr->class instanceof Node\Name) {
                    if ($types = $this->typeResolver->resolvePHPParserTypes($typeScope, $assignExprExpr->class)) {
                        $typeScope->assignVariable($var->name, ...$types);
                    }
                }

                if ($assignExprExpr instanceof Node\Expr\MethodCall && $assignExprExpr->name instanceof Node\Identifier && $assignExprExpr->var instanceof Node\Expr\Variable) {
                    if ($varTypes = $typeScope->getVariable($assignExprExpr->var->name)) {
                        foreach ($varTypes as $varType) {
                            $typeScope->assignVariable($var->name, 'TODO'); // TODO get reflection for $varType
                        }
                    }
                }
            }

            if ($stmt->expr instanceof Node\Expr\MethodCall && $stmt->expr->var instanceof Node\Expr\Variable) {
                /** @var Node\Expr\Variable $var */
                $var = $stmt->expr->var;
                if ($varTypes = $typeScope->getVariable((string) $var->name)) {
                    foreach ($varTypes as $varType) {
                        $classReferenceBuilder->methodCall($varType, $stmt->expr->getStartLine());
                    }
                }
            }
        }
    }
}
