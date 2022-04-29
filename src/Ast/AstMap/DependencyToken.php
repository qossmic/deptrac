<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\AstMap;

/**
 * @psalm-immutable
 */
class DependencyToken
{
    public const USE = 'use';
    public const RETURN_TYPE = 'returntype';
    public const PARAMETER = 'parameter';
    public const NEW = 'new';
    public const STATIC_PROPERTY = 'static_property';
    public const STATIC_METHOD = 'static_method';
    public const INSTANCEOF = 'instanceof';
    public const CATCH = 'catch';
    public const VARIABLE = 'variable';
    public const THROW = 'throw';
    public const CONST = 'const';
    public const ANONYMOUS_CLASS_EXTENDS = 'anonymous_class_extends';
    public const ANONYMOUS_CLASS_IMPLEMENTS = 'anonymous_class_implements';
    public const ANONYMOUS_CLASS_TRAIT = 'anonymous_class_trait';
    public const ATTRIBUTE = 'attribute';
    public const SUPERGLOBAL_VARIABLE = 'superglobal_variable';

    private TokenInterface $token;
    private FileOccurrence $fileOccurrence;
    private string $type;

    private function __construct(TokenInterface $token, FileOccurrence $fileOccurrence, string $type)
    {
        $this->token = $token;
        $this->fileOccurrence = $fileOccurrence;
        $this->type = $type;
    }

    public function getToken(): TokenInterface
    {
        return $this->token;
    }

    public function getFileOccurrence(): FileOccurrence
    {
        return $this->fileOccurrence;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public static function fromType(TokenInterface $tokenName, FileOccurrence $fileOccurrence, string $type): self
    {
        return new self($tokenName, $fileOccurrence, $type);
    }
}
