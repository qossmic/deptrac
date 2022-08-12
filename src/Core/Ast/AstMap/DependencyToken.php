<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap;

/**
 * @psalm-immutable
 */
class DependencyToken
{
    final public const USE = 'use';
    final public const RETURN_TYPE = 'returntype';
    final public const PARAMETER = 'parameter';
    final public const NEW = 'new';
    final public const STATIC_PROPERTY = 'static_property';
    final public const STATIC_METHOD = 'static_method';
    final public const INSTANCEOF = 'instanceof';
    final public const CATCH = 'catch';
    final public const VARIABLE = 'variable';
    final public const THROW = 'throw';
    final public const CONST = 'const';
    final public const ANONYMOUS_CLASS_EXTENDS = 'anonymous_class_extends';
    final public const ANONYMOUS_CLASS_IMPLEMENTS = 'anonymous_class_implements';
    final public const ANONYMOUS_CLASS_TRAIT = 'anonymous_class_trait';
    final public const ATTRIBUTE = 'attribute';
    final public const SUPERGLOBAL_VARIABLE = 'superglobal_variable';
    final public const UNRESOLVED_FUNCTION_CALL = 'unresolved_function_call';

    private function __construct(
        public readonly TokenInterface $token,
        public readonly FileOccurrence $fileOccurrence,
        public readonly string $type
    ) {
    }

    public static function fromType(TokenInterface $tokenName, FileOccurrence $fileOccurrence, string $type): self
    {
        return new self($tokenName, $fileOccurrence, $type);
    }
}
