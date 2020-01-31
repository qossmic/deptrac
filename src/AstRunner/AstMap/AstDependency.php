<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

class AstDependency
{
    private $class;
    private $fileOccurrence;
    private $type;

    private function __construct(ClassLikeName $class, FileOccurrence $fileOccurrence, string $type)
    {
        $this->class = $class;
        $this->fileOccurrence = $fileOccurrence;
        $this->type = $type;
    }

    public function getClassLikeName(): ClassLikeName
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

    public static function useStmt(ClassLikeName $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'use');
    }

    public static function returnType(ClassLikeName $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'returntype');
    }

    public static function parameter(ClassLikeName $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'parameter');
    }

    public static function newStmt(ClassLikeName $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'new');
    }

    public static function staticProperty(ClassLikeName $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'static_property');
    }

    public static function staticMethod(ClassLikeName $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'static_method');
    }

    public static function instanceofExpr(ClassLikeName $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'instanceof');
    }

    public static function catchStmt(ClassLikeName $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'catch');
    }

    public static function variable(ClassLikeName $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'variable');
    }

    public static function throwStmt(ClassLikeName $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'throw');
    }

    public static function constFetch(ClassLikeName $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'const');
    }

    public static function anonymousClassExtends(ClassLikeName $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'anonymous_class_extends');
    }

    public static function anonymousClassImplements(ClassLikeName $class, FileOccurrence $fileOccurrence): self
    {
        return new self($class, $fileOccurrence, 'anonymous_class_implements');
    }
}
