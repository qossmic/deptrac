<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;

class TypeResolver
{
    private const BUILTIN_TYPES = [
        'array',
        'callable',
        'string',
        'int',
        'float',
        'double',
        'bool',
        'iterable',
        'void',
        'object',
    ];

    private $nameScope;

    public function __construct(NameScope $nameScope)
    {
        $this->nameScope = $nameScope;
    }

    /**
     * @return string[]
     */
    public function resolveType(TypeNode $type): array
    {
        if ($type instanceof IdentifierTypeNode && !$this->isBuiltinType($type->name)) {
            return [$this->nameScope->resolveStringName($type->name)];
        }
        if ($type instanceof NullableTypeNode) {
            return $this->resolveType($type->type);
        }
        if ($type instanceof ArrayTypeNode) {
            return $this->resolveType($type->type);
        }

        return [];
    }

    private function isBuiltinType(string $type): bool
    {
        return in_array($type, self::BUILTIN_TYPES, true);
    }
}
