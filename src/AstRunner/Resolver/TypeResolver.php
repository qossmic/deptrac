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
    public function resolvePHPParserTypes(Context $context, NodeAbstract ...$nodes): array
    {
        $types = [];
        foreach ($nodes as $node) {
            $types[] = $this->resolvePHPParserType($context, $node);
        }

        return array_merge([], ...$types);
    }

    /**
     * @return string[]
     */
    private function resolvePHPParserType(Context $context, NodeAbstract $node): array
    {
        if ($node instanceof Node\Name && $node->isSpecialClassName()) {
            return [];
        }

        if ($node instanceof Node\Name) {
            return $this->resolveString($node->toCodeString(), $context);
        }

        if ($node instanceof Node\NullableType) {
            return $this->resolvePHPParserType($context, $node->type);
        }

        if ($node instanceof Node\UnionType) {
            return $this->resolvePHPParserTypes($context, ...$node->types);
        }

        return [];
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
