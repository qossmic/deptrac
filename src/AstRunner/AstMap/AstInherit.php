<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

class AstInherit implements AstInheritInterface
{
    private $className;
    private $line;
    private $type;

    private function __construct(string $className, int $line, int $type)
    {
        $this->className = $className;
        $this->line = $line;
        $this->type = $type;
    }

    public static function newExtends(string $className, int $line): self
    {
        return new self($className, $line, AstInheritInterface::TYPE_EXTENDS);
    }

    public static function newImplements(string $className, int $line): self
    {
        return new self($className, $line, AstInheritInterface::TYPE_IMPLEMENTS);
    }

    public static function newUses(string $className, int $line): self
    {
        return new self($className, $line, AstInheritInterface::TYPE_USES);
    }

    public function __toString(): string
    {
        switch ($this->type) {
            case static::TYPE_EXTENDS:
                $type = 'Extends';
                break;
            case static::TYPE_USES:
                $type = 'Uses';
                break;
            case static::TYPE_IMPLEMENTS:
                $type = 'Implements';
                break;
            default:
                $type = 'Unknown';
        }

        return "{$this->className}::{$this->line} ($type)";
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return AstInheritInterface[]
     */
    public function getPath(): array
    {
        return [];
    }
}
