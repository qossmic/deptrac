<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser;

use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver as phpDocumentorTypeResolver;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Object_;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
use PhpParser\NodeAbstract;
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
use Throwable;

class TypeResolver
{
    private phpDocumentorTypeResolver $typeResolver;

    public function __construct()
    {
        $this->typeResolver = new phpDocumentorTypeResolver(new FqsenResolver());
    }

    /**
     * @return string[]
     */
    public function resolvePHPParserTypes(TypeScope $typeScope, NodeAbstract ...$nodes): array
    {
        $types = [];
        foreach ($nodes as $node) {
            $types[] = $this->resolvePHPParserType($typeScope, $node);
        }

        return array_merge([], ...$types);
    }

    /**
     * @return string[]
     */
    private function resolvePHPParserType(TypeScope $typeScope, NodeAbstract $node): array
    {
        if ($node instanceof Name && $node->isSpecialClassName()) {
            return [];
        }

        if ($node instanceof Name) {
            return $this->resolveString($node->toCodeString(), $typeScope);
        }

        if ($node instanceof NullableType) {
            return $this->resolvePHPParserType($typeScope, $node->type);
        }

        if ($node instanceof UnionType) {
            return $this->resolvePHPParserTypes($typeScope, ...$node->types);
        }

        if ($node instanceof IntersectionType) {
            return $this->resolvePHPParserTypes($typeScope, ...$node->types);
        }

        return [];
    }

    /**
     * @param array<string> $templateTypes
     *
     * @return string[]
     */
    public function resolvePHPStanDocParserType(TypeNode $type, TypeScope $typeScope, array $templateTypes = []): array
    {
        if ($type instanceof IdentifierTypeNode) {
            if (in_array($type->name, $templateTypes, true)) {
                return [];
            }

            return $this->resolveString($type->name, $typeScope);
        }
        if ($type instanceof ConstTypeNode && $type->constExpr instanceof ConstFetchNode) {
            return $this->resolveString($type->constExpr->className, $typeScope);
        }
        if ($type instanceof NullableTypeNode) {
            return $this->resolvePHPStanDocParserType($type->type, $typeScope, $templateTypes);
        }
        if ($type instanceof ArrayTypeNode) {
            return $this->resolvePHPStanDocParserType($type->type, $typeScope, $templateTypes);
        }
        if ($type instanceof UnionTypeNode || $type instanceof IntersectionTypeNode) {
            return array_merge([], ...array_map(function (TypeNode $typeNode) use ($typeScope, $templateTypes) {
                return $this->resolvePHPStanDocParserType($typeNode, $typeScope, $templateTypes);
            }, $type->types));
        }
        if ($type instanceof GenericTypeNode) {
            $preType = 'list' === $type->type->name ? [] : $this->resolvePHPStanDocParserType($type->type, $typeScope, $templateTypes);

            return array_merge($preType, ...array_map(function (TypeNode $typeNode) use ($typeScope, $templateTypes) {
                return $this->resolvePHPStanDocParserType($typeNode, $typeScope, $templateTypes);
            }, $type->genericTypes));
        }
        if ($type instanceof ArrayShapeNode) {
            return array_merge([], ...array_map(function (ArrayShapeItemNode $itemNode) use ($typeScope, $templateTypes) {
                return $this->resolvePHPStanDocParserType($itemNode->valueType, $typeScope, $templateTypes);
            }, $type->items)
            );
        }
        if ($type instanceof CallableTypeNode) {
            return array_merge(
                $this->resolvePHPStanDocParserType($type->returnType, $typeScope, $templateTypes),
                ...array_map(function (CallableTypeParameterNode $parameterNode) use ($typeScope, $templateTypes) {
                    return $this->resolvePHPStanDocParserType($parameterNode->type, $typeScope, $templateTypes);
                }, $type->parameters)
            );
        }

        return $this->resolveString((string) $type, $typeScope);
    }

    /**
     * @return string[]
     */
    public function resolveString(string $type, TypeScope $nameScope): array
    {
        $context = new Context($nameScope->getNamespace(), $nameScope->getUses());
        try {
            $resolvedType = $this->typeResolver->resolve($type, $context);
        } catch (Throwable $e) {
            return [];
        }

        return $this->resolveReflectionType($resolvedType);
    }

    /**
     * @param Identifier|Name|ComplexType $type
     *
     * @return string[]
     */
    public function resolvePropertyType(NodeAbstract $type): array
    {
        if ($type instanceof FullyQualified) {
            return [(string) $type];
        }
        if ($type instanceof NullableType) {
            return $this->resolvePropertyType($type->type);
        }
        if ($type instanceof UnionType || $type instanceof IntersectionType) {
            return array_merge([], ...array_map(
                /**
                 * @param Identifier|Name $typeNode
                 */
                function ($typeNode) {
                    return $this->resolvePropertyType($typeNode);
                },
            $type->types));
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function resolveReflectionType(Type $resolvedType): array
    {
        if ($resolvedType instanceof Object_) {
            return ($fqsen = $resolvedType->getFqsen()) ? [(string) $fqsen] : [];
        }

        if ($resolvedType instanceof Compound) {
            return array_merge([], ...array_map(function (Type $type) {
                return $this->resolveReflectionType($type);
            }, iterator_to_array($resolvedType)));
        }

        return [];
    }
}
