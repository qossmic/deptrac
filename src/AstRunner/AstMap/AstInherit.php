<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

class AstInherit
{
    private const TYPE_EXTENDS = 1;
    private const TYPE_IMPLEMENTS = 2;
    private const TYPE_USES = 3;

    private $className;
    private $fileOccurrence;
    private $type;

    /** @var AstInherit[] */
    private $path;

    private function __construct(string $className, FileOccurrence $fileOccurrence, int $type)
    {
        $this->className = $className;
        $this->fileOccurrence = $fileOccurrence;
        $this->type = $type;
        $this->path = [];
    }

    public static function newExtends(string $className, FileOccurrence $fileOccurrence): self
    {
        return new self($className, $fileOccurrence, static::TYPE_EXTENDS);
    }

    public static function newImplements(string $className, FileOccurrence $fileOccurrence): self
    {
        return new self($className, $fileOccurrence, static::TYPE_IMPLEMENTS);
    }

    public static function newTraitUse(string $className, FileOccurrence $fileOccurrence): self
    {
        return new self($className, $fileOccurrence, static::TYPE_USES);
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

        $description = "{$this->className}::{$this->fileOccurrence->getLine()} ($type)";

        if (0 === count($this->path)) {
            return $description;
        }

        $buffer = '';
        foreach ($this->path as $v) {
            $buffer = $v.' -> '.$buffer;
        }

        return $description.' (path: '.rtrim($buffer, ' -> ').')';
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getFileOccurrence(): FileOccurrence
    {
        return $this->fileOccurrence;
    }

    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return AstInherit[]
     */
    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * @param AstInherit[] $path
     */
    public function withPath(array $path): self
    {
        $self = clone $this;
        $self->path = $path;

        return $self;
    }
}
