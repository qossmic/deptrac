<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Object_;
use PhpParser\Node;
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

class TypeResolver
{
    // Can be removed when https://github.com/phpDocumentor/TypeResolver/issues/111 gets merged.
    private const PSEUDO_TYPES = [
        'array-key',
        'trait-string',
        'callable-string',
        'numeric-string',
        'positive-int',
        'non-empty-array',
        'callable-array',
    ];

    /**
     * @var \phpDocumentor\Reflection\TypeResolver
     */
    private $typeResolver;

    public function __construct()
    {
        $this->typeResolver = new \phpDocumentor\Reflection\TypeResolver(new FqsenResolver());
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
        if ($node instanceof Node\Name && $node->isSpecialClassName()) {
            return [];
        }

        if ($node instanceof Node\Name) {
            return $this->resolveString($node->toCodeString(), $typeScope);
        }

        if ($node instanceof Node\NullableType) {
            return $this->resolvePHPParserType($typeScope, $node->type);
        }

        if ($node instanceof Node\UnionType) {
            return $this->resolvePHPParserTypes($typeScope, ...$node->types);
        }

        return [];
    }

    /**
     * @return string[]
     */
    public function resolvePHPStanDocParserType(TypeNode $type, TypeScope $typeScope): array
    {
        if ($type instanceof IdentifierTypeNode) {
            return $this->resolveString($type->name, $typeScope);
        }
        if ($type instanceof ConstTypeNode && $type->constExpr instanceof ConstFetchNode) {
            return $this->resolveString($type->constExpr->className, $typeScope);
        }
        if ($type instanceof NullableTypeNode) {
            return $this->resolvePHPStanDocParserType($type->type, $typeScope);
        }
        if ($type instanceof ArrayTypeNode) {
            return $this->resolvePHPStanDocParserType($type->type, $typeScope);
        }
        if ($type instanceof UnionTypeNode || $type instanceof IntersectionTypeNode) {
            return array_merge([], ...array_map(function (TypeNode $typeNode) use ($typeScope) {
                return $this->resolvePHPStanDocParserType($typeNode, $typeScope);
            }, $type->types));
        }
        if ($type instanceof GenericTypeNode) {
            return array_merge([], ...array_map(function (TypeNode $typeNode) use ($typeScope) {
                return $this->resolvePHPStanDocParserType($typeNode, $typeScope);
            }, $type->genericTypes));
        }
        if ($type instanceof ArrayShapeNode) {
            return array_merge([], ...array_map(function (ArrayShapeItemNode $itemNode) use ($typeScope) {
                return $this->resolvePHPStanDocParserType($itemNode->valueType, $typeScope);
            }, $type->items)
            );
        }
        if ($type instanceof CallableTypeNode) {
            return array_merge(
                $this->resolvePHPStanDocParserType($type->returnType, $typeScope),
                ...array_map(function (CallableTypeParameterNode $parameterNode) use ($typeScope) {
                    return $this->resolvePHPStanDocParserType($parameterNode->type, $typeScope);
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
        if (in_array($type, self::PSEUDO_TYPES, true)) {
            return [];
        }

        $context = new Context($nameScope->getNamespace(), $nameScope->getUses());
        $resolvedType = $this->typeResolver->resolve($type, $context);

        return $this->resolveReflectionType($resolvedType);
    }

    /**
     * @param \PhpParser\Node\Identifier|\PhpParser\Node\Name|\PhpParser\Node\NullableType|\PhpParser\Node\UnionType $type
     *
     * @return string[]
     */
    public function resolvePropertyType(NodeAbstract $type): array
    {
        if ($type instanceof Node\Name\FullyQualified) {
            return [(string) $type];
        }
        if ($type instanceof Node\NullableType) {
            return $this->resolvePropertyType($type->type);
        }
        if ($type instanceof Node\UnionType) {
            return array_merge([], ...array_map(function (NodeAbstract $typeNode) {
                /** @var \PhpParser\Node\Identifier|\PhpParser\Node\Name $typeNode */
                return $this->resolvePropertyType($typeNode);
            }, $type->types));
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
