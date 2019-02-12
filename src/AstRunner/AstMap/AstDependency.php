<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

class AstDependency
{
    private $class;

    private $line;

    private $type;

    public function __construct(string $class, int $line, string $type)
    {
        $this->class = $class;
        $this->line = $line;
        $this->type = $type;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public static function returnType(string $class, int $line): self
    {
        return new self($class, $line, 'returntype');
    }

    public static function parameter(string $class, int $line): self
    {
        return new self($class, $line, 'parameter');
    }

    public static function newStmt(string $class, int $line): self
    {
        return new self($class, $line, 'new');
    }

    public static function staticProperty(string $class, int $line): self
    {
        return new self($class, $line, 'static_property');
    }

    public static function staticMethod(string $class, int $line): self
    {
        return new self($class, $line, 'static_method');
    }

    public static function instanceof(string $class, int $line): self
    {
        return new self($class, $line, 'instanceof');
    }

    public static function catchStmt(string $class, int $line): self
    {
        return new self($class, $line, 'catch');
    }
}
