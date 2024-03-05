<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser;

use Error;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
use PHPStan\Analyser\Scope;

class TypeResolver
{
    /**
     * @return list<string>
     */
    public static function resolveComplexType(ComplexType|Name|Identifier|null $type, Scope $scope): array
    {
        if (null === $type) {
            return [];
        }

        return match (true) {
            $type instanceof Name => [$scope->resolveName($type)],
            $type instanceof Identifier => [],
            $type instanceof NullableType => self::resolveComplexType($type->type, $scope),
            $type instanceof IntersectionType, $type instanceof UnionType => array_merge(...array_map(static fn ($type): array => self::resolveComplexType($type, $scope), $type->types)),
            default => throw new Error(get_class($type)),
        };
    }
}
