<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\PhpdocParser;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Object_;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstFetchNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeItemNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeParameterNode;
use PHPStan\PhpDocParser\Ast\Type\ConstTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;

final class ResolveTypeNames
{
    private $typeResolver;

    public function __construct()
    {
        $this->typeResolver = new TypeResolver();
    }

    public function __invoke(TypeNode $type, Context $context)
    {
        if ($type instanceof IdentifierTypeNode) {
            return $this->resolveString($type->name, $context);
        }
        if ($type instanceof ConstTypeNode && $type->constExpr instanceof ConstFetchNode) {
            return $this->resolveString($type->constExpr->className, $context);
        }
        if ($type instanceof NullableTypeNode) {
            return $this->__invoke($type->type, $context);
        }
        if ($type instanceof ArrayTypeNode) {
            return $this->__invoke($type->type, $context);
        }
        if ($type instanceof UnionTypeNode || $type instanceof IntersectionTypeNode) {
            return array_merge(
                [],
                ...array_map(
                    function (TypeNode $typeNode) use ($context) {
                        return $this->__invoke($typeNode, $context);
                    },
                    $type->types
                )
            );
        }
        if ($type instanceof GenericTypeNode) {
            return array_merge(
                [],
                ...array_map(
                    function (TypeNode $typeNode) use ($context) {
                        return $this->__invoke($typeNode, $context);
                    },
                    $type->genericTypes
                )
            );
        }
        if ($type instanceof ArrayShapeNode) {
            return array_merge(
                [],
                ...array_map(
                    function (ArrayShapeItemNode $itemNode) use ($context) {
                        return $this->__invoke($itemNode->valueType, $context);
                    },
                    $type->items
                )
            );
        }
        if ($type instanceof CallableTypeNode) {
            return array_merge(
                $this->__invoke($type->returnType, $context),
                ...array_map(
                    function (CallableTypeParameterNode $parameterNode) use ($context) {
                        return $this->__invoke($parameterNode->type, $context);
                    },
                    $type->parameters
                )
            );
        }

        return $this->resolveString((string)$type, $context);
    }

    /**
     * @return string[]
     */
    public function resolveString(string $type, Context $context): array
    {
        $resolvedType = $this->typeResolver->resolve($type, $context);

        return $this->resolveReflectionType($resolvedType);
    }

    /**
     * @return string[]
     */
    private function resolveReflectionType(Type $resolvedType): array
    {
        if ($resolvedType instanceof Object_) {
            return ($fqsen = $resolvedType->getFqsen()) ? [(string)$fqsen] : [];
        }

        if ($resolvedType instanceof Compound) {
            return array_merge(
                [],
                ...array_map(
                function (Type $type) {
                    return $this->resolveReflectionType($type);
                },
                iterator_to_array($resolvedType)
            )
            );
        }

        return [];
    }
}
