<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

class AstDependency
{
    private $class;
    private $fileOccurrence;
    private $type;

    public function __construct(string $class, FileOccurrence $fileOccurrence, string $type)
    {
        $this->class = $class;
        $this->fileOccurrence = $fileOccurrence;
        $this->type = $type;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getFileOccurrence(): FileOccurrence
    {
        return $this->fileOccurrence;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public static function useStmt(string $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'use');
    }

    public static function returnType(string $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'returntype');
    }

    public static function parameter(string $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'parameter');
    }

    public static function newStmt(string $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'new');
    }

    public static function staticProperty(string $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'static_property');
    }

    public static function staticMethod(string $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'static_method');
    }

    public static function instanceofExpr(string $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'instanceof');
    }

    public static function catchStmt(string $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'catch');
    }

    public static function variable(string $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'variable');
    }

    public static function throwStmt(string $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'throw');
    }

    public static function constFetch(string $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'const');
    }

    public static function anonymousClassExtends(string $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'anonymous_class_extends');
    }

    public static function anonymousClassImplements(string $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'anonymous_class_implements');
    }
}
