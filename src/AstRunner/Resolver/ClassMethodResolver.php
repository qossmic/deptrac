<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use Qossmic\Deptrac\AstRunner\AstMap\ReferenceBuilder;
use Roave\BetterReflection\BetterReflection;

class ClassMethodResolver implements DependencyResolver
{
    private TypeResolver $typeResolver;
    private BetterReflection $reflector;

    public function __construct(TypeResolver $typeResolver, BetterReflection $reflector)
    {
        $this->typeResolver = $typeResolver;
        $this->reflector = $reflector;
    }

    public function processNode(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        if (!$node instanceof Node\Stmt\ClassMethod) {
            return;
        }

        $variablesCache = [];

        foreach ($node->stmts ?? [] as $stmt) {
            if (!$stmt instanceof Node\Stmt\Expression) {
                continue;
            }

            $retType = null;

            if ($stmt->expr instanceof Node\Expr\Assign && $stmt->expr->var instanceof Node\Expr\Variable) {
                $assignExpr = $stmt->expr;
                /** @var Node\Expr\Variable $var */
                $var = $assignExpr->var;
                $assignExprExpr = $assignExpr->expr;

                if ($assignExprExpr instanceof Node\Expr\New_ && $assignExprExpr->class instanceof Node\Name) {
                    if ($type = $this->typeResolver->resolvePHPParserTypes($typeScope, $assignExprExpr->class)[0] ?? null) { // TODO handle union types
                        $variablesCache[$var->name] = $type;
                    }
                }

                if ($assignExprExpr instanceof Node\Expr\MethodCall) {
                    $retType = $this->getReturnTypeFromMethodCall($assignExprExpr, $variablesCache, $referenceBuilder);
                    $variablesCache[$var->name] = $retType;
                }
            }

            if ($stmt->expr instanceof Node\Expr\MethodCall) {
                $retType = $this->getReturnTypeFromMethodCall($stmt->expr, $variablesCache, $referenceBuilder);
            }

            if ($stmt->expr->var instanceof Node\Expr\Variable && $retType !== null) {
                $referenceBuilder->methodCall($retType, $stmt->expr->getStartLine());
            }
        }
    }

    private function getReturnTypeFromMethodCall(Node\Expr\MethodCall $expr, array &$variablesCache, ReferenceBuilder $referenceBuilder)
    {
        if ($expr->var instanceof Node\Expr\Variable) {
            $varType = $variablesCache[$expr->var->name] ?? null;
        } else if ($expr->var instanceof Node\Expr\MethodCall) {
            $varType = $this->getReturnTypeFromMethodCall($expr->var, $variablesCache, $referenceBuilder);
            if ($varType !== null) {
                $referenceBuilder->methodCall($varType, $expr->getStartLine());
            }
        }

        if ($varType !== null) {
            $classInfo = $this->reflector->classReflector()->reflect($varType);
            if ($classInfo->hasMethod($expr->name->name)) {
                $type = $classInfo->getMethod($expr->name->name)->getReturnType();
                if ($type !== null) {
                    return $type->getName();
                }
            }
        }

        return null;
    }
}
