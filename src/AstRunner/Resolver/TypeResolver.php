<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Object_;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;

class TypeResolver
{
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
    public function resolveType(TypeNode $type, NameScope $nameScope): array
    {
        if ($type instanceof IdentifierTypeNode) {
            return $this->resolveString($type->name, $nameScope);
        }
        if ($type instanceof NullableTypeNode) {
            return $this->resolveType($type->type, $nameScope);
        }
        if ($type instanceof ArrayTypeNode) {
            return $this->resolveType($type->type, $nameScope);
        }
        if ($type instanceof UnionTypeNode || $type instanceof IntersectionTypeNode) {
            return array_merge([], ...array_map(function (TypeNode $typeNode) use ($nameScope) {
                return $this->resolveType($typeNode, $nameScope);
            }, $type->types));
        }

        return $this->resolveString((string) $type, $nameScope);
    }

    public function resolveString(string $type, NameScope $nameScope): array
    {
        $context = new Context($nameScope->getNamespace(), $nameScope->getUses());
        $resolvedType = $this->typeResolver->resolve($type, $context);

        return $this->resolveReflectionType($resolvedType);
    }

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
