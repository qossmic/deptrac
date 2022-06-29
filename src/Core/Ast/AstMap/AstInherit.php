<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap;

use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use function array_reverse;
use function implode;
use function sprintf;

/**
 * @psalm-immutable
 */
class AstInherit
{
    private const TYPE_EXTENDS = 1;
    private const TYPE_IMPLEMENTS = 2;
    private const TYPE_USES = 3;

    private ClassLikeToken $classLikeName;
    private FileOccurrence $fileOccurrence;
    private int $type;
    /** @var AstInherit[] */
    private array $path;

    private function __construct(ClassLikeToken $className, FileOccurrence $fileOccurrence, int $type)
    {
        $this->classLikeName = $className;
        $this->fileOccurrence = $fileOccurrence;
        $this->type = $type;
        $this->path = [];
    }

    public static function newExtends(ClassLikeToken $className, FileOccurrence $fileOccurrence): self
    {
        return new self($className, $fileOccurrence, self::TYPE_EXTENDS);
    }

    public static function newImplements(ClassLikeToken $className, FileOccurrence $fileOccurrence): self
    {
        return new self($className, $fileOccurrence, self::TYPE_IMPLEMENTS);
    }

    public static function newTraitUse(ClassLikeToken $className, FileOccurrence $fileOccurrence): self
    {
        return new self($className, $fileOccurrence, self::TYPE_USES);
    }

    public function __toString(): string
    {
        switch ($this->type) {
            case self::TYPE_EXTENDS:
                $type = 'Extends';
                break;
            case self::TYPE_USES:
                $type = 'Uses';
                break;
            case self::TYPE_IMPLEMENTS:
                $type = 'Implements';
                break;
            default:
                $type = 'Unknown';
        }

        $description = "{$this->classLikeName->toString()}::{$this->fileOccurrence->getLine()} ($type)";

        if (0 === count($this->path)) {
            return $description;
        }

        return sprintf('%s (path: %s)', $description, implode(' -> ', array_reverse($this->path)));
    }

    public function getClassLikeName(): ClassLikeToken
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
