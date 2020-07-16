<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

class AstInherit
{
    private const TYPE_EXTENDS = 1;
    private const TYPE_IMPLEMENTS = 2;
    private const TYPE_USES = 3;

    /** @var ClassLikeName */
    private $classLikeName;
    /** @var FileOccurrence */
    private $fileOccurrence;
    /** @var int */
    private $type;
    /** @var AstInherit[] */
    private $path;

    private function __construct(ClassLikeName $className, FileOccurrence $fileOccurrence, int $type)
    {
        $this->classLikeName = $className;
        $this->fileOccurrence = $fileOccurrence;
        $this->type = $type;
        $this->path = [];
    }

    public static function newExtends(ClassLikeName $className, FileOccurrence $fileOccurrence): self
    {
        return new self($className, $fileOccurrence, self::TYPE_EXTENDS);
    }

    public static function newImplements(ClassLikeName $className, FileOccurrence $fileOccurrence): self
    {
        return new self($className, $fileOccurrence, self::TYPE_IMPLEMENTS);
    }

    public static function newTraitUse(ClassLikeName $className, FileOccurrence $fileOccurrence): self
    {
        return new self($className, $fileOccurrence, self::TYPE_USES);
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

        $description = "{$this->classLikeName->toString()}::{$this->fileOccurrence->getLine()} ($type)";

        if (0 === count($this->path)) {
            return $description;
        }

        $buffer = '';
        foreach ($this->path as $v) {
            $buffer = $v.' -> '.$buffer;
        }

        return $description.' (path: '.rtrim($buffer, ' -> ').')';
    }

    public function getClassLikeName(): ClassLikeName
    {
        return $this->classLikeName;
    }

    public function getFileOccurrence(): FileOccurrence
    {
        return $this->fileOccurrence;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function isImplements(): bool
    {
        return self::TYPE_IMPLEMENTS === $this->type;
    }

    public function isExtends(): bool
    {
        return self::TYPE_EXTENDS === $this->type;
    }

    public function isUses(): bool
    {
        return self::TYPE_USES === $this->type;
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
