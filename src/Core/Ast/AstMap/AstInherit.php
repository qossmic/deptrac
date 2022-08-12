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
    /**
     * @var AstInherit[]
     */
    private array $path;

    public function __construct(
        public readonly ClassLikeToken $classLikeName,
        public readonly FileOccurrence $fileOccurrence,
        public readonly AstInheritType $type
    ) {
        $this->path = [];
    }

    /**
     * @return AstInherit[]
     */
    public function getPath(): array
    {
        return $this->path;
    }

    public function __toString(): string
    {
        $description = "{$this->classLikeName->toString()}::{$this->fileOccurrence->line} ({$this->type->value})";

        if (0 === count($this->path)) {
            return $description;
        }

        return sprintf('%s (path: %s)', $description, implode(' -> ', array_reverse($this->path)));
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
