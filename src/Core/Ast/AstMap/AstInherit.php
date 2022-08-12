<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap;

use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Stringable;
use function array_reverse;
use function implode;
use function sprintf;

/**
 * @psalm-immutable
 */
class AstInherit implements Stringable
{
    private const TYPE_EXTENDS = 1;
    private const TYPE_IMPLEMENTS = 2;
    private const TYPE_USES = 3;
    /** @var AstInherit[] */
    private array $path;

    private function __construct(private readonly ClassLikeToken $classLikeName, private readonly FileOccurrence $fileOccurrence, private readonly int $type)
    {
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
        $type = match ($this->type) {
            self::TYPE_EXTENDS => 'Extends',
            self::TYPE_USES => 'Uses',
            self::TYPE_IMPLEMENTS => 'Implements',
            default => 'Unknown',
        };

        $description = "{$this->classLikeName->toString()}::{$this->fileOccurrence->line} ($type)";

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
